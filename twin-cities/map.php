<?php
require_once 'db.php';
$pdo = getDB();

$cities = $pdo->query("SELECT * FROM city")->fetchAll();
$pois   = $pdo->query("
    SELECT p.*, c.name AS city_name, cat.name AS category
    FROM place_of_interest p
    JOIN city c ON p.city_id = c.id
    JOIN category cat ON p.category_id = cat.id
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maps — Twin Cities</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>
<header>
    <h1>🌍 Twin &amp; Sister Cities — Birmingham</h1>
    <nav>
    <a href="index.php">Home</a>
    <a href="map.php">Maps</a>
    <a href="weather.php">Weather</a>
    <a href="photos.php">Photos</a>
    <a href="rss.php">RSS Feed</a>
</nav>
</header>

<div class="container">
    <div class="card">
        <h2>City Maps</h2>
        <div class="map-tabs">
            <?php foreach ($cities as $i => $city): ?>
            <button onclick="showMap(<?= $i ?>)" id="tab-<?= $i ?>" class="<?= $i===0?'active':'' ?>">
                <?= htmlspecialchars($city['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($cities as $i => $city): ?>
        <div id="map-<?= $i ?>" style="<?= $i===0?'':'display:none;' ?>">
            <div id="leaflet-<?= $i ?>" style="height:500px; border-radius:8px;"></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
const maps = [];
const cityData = <?= json_encode($cities) ?>;
const poiData  = <?= json_encode($pois) ?>;

const categoryIcons = {
    'Stadium':       '🏟️',
    'University':    '🎓',
    'Cathedral':     '⛪',
    'Airport':       '✈️',
    'Railway Station': '🚂',
    'Museum':        '🏛️',
    'Concert Hall':  '🎵',
    'Library':       '📚'
};

function initMap(index) {
    if (maps[index]) return;
    const city = cityData[index];
    const map = L.map('leaflet-' + index).setView([city.latitude, city.longitude], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // City centre marker
    L.marker([city.latitude, city.longitude])
        .addTo(map)
        .bindPopup('<strong>' + city.name + '</strong><br>' + city.country);

    // POI markers
    poiData.filter(p => p.city_id == city.id).forEach(poi => {
        const icon = categoryIcons[poi.category] || '📍';
        const divIcon = L.divIcon({
            html: '<div style="font-size:1.6rem; cursor:pointer;">' + icon + '</div>',
            className: '',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const marker = L.marker([poi.latitude, poi.longitude], { icon: divIcon }).addTo(map);

        // Mouseover tooltip
        marker.bindTooltip(
            '<strong>' + poi.name + '</strong><br>' +
            '<em>' + poi.category + '</em>' +
            (poi.capacity ? '<br>Capacity: ' + poi.capacity.toLocaleString() : ''),
            { direction: 'top' }
        );

        // Click → detail page
        marker.on('click', function() {
            window.location.href = 'poi.php?id=' + poi.id;
        });
    });

    maps[index] = map;
}

function showMap(index) {
    cityData.forEach((_, i) => {
        document.getElementById('map-' + i).style.display = 'none';
        document.getElementById('tab-' + i).classList.remove('active');
    });
    document.getElementById('map-' + index).style.display = 'block';
    document.getElementById('tab-' + index).classList.add('active');
    initMap(index);
    setTimeout(() => maps[index] && maps[index].invalidateSize(), 100);
}

// Init first map on load
initMap(0);
</script>

<footer>Twin &amp; Sister Cities — UFCFV4-30-2 Data, Schemas and Applications</footer>
</body>
</html>