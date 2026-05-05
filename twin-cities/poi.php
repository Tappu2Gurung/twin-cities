<?php
require_once 'db.php';
$pdo = getDB();

$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$poi = $pdo->prepare("
    SELECT p.*, c.name AS city_name, cat.name AS category
    FROM place_of_interest p
    JOIN city c ON p.city_id = c.id
    JOIN category cat ON p.category_id = cat.id
    WHERE p.id = ?
");
$poi->execute([$id]);
$poi = $poi->fetch();

if (!$poi) { header("Location: map.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($poi['name']) ?> — Twin Cities</title>
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
    <div class="card poi-detail">
        <a href="map.php" style="color:#1a3c5e;">&larr; Back to Maps</a>
        <h2 style="margin:15px 0;"><?= htmlspecialchars($poi['name']) ?></h2>

        <?php if ($poi['photo_url']): ?>
        <img src="<?= htmlspecialchars($poi['photo_url']) ?>" alt="<?= htmlspecialchars($poi['name']) ?>">
        <?php endif; ?>

        <table style="margin-top:10px;">
            <tr><th>City</th><td><?= htmlspecialchars($poi['city_name']) ?></td></tr>
            <tr><th>Category</th><td><?= htmlspecialchars($poi['category']) ?></td></tr>
            <?php if ($poi['capacity']): ?>
            <tr><th>Capacity</th><td><?= number_format($poi['capacity']) ?></td></tr>
            <?php endif; ?>
            <tr><th>Location</th><td><?= $poi['latitude'] ?>, <?= $poi['longitude'] ?></td></tr>
        </table>

        <p style="margin-top:15px;"><?= htmlspecialchars($poi['description']) ?></p>

        <?php if ($poi['wikipedia_url']): ?>
        <p style="margin-top:12px;">
            <a href="<?= htmlspecialchars($poi['wikipedia_url']) ?>" target="_blank" style="color:#1a3c5e;">
                📖 Read more on Wikipedia
            </a>
        </p>
        <?php endif; ?>

        <div id="poi-map" style="height:300px; margin-top:20px; border-radius:8px;"></div>
    </div>
</div>

<script>
const map = L.map('poi-map').setView([<?= $poi['latitude'] ?>, <?= $poi['longitude'] ?>], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);
L.marker([<?= $poi['latitude'] ?>, <?= $poi['longitude'] ?>])
    .addTo(map)
    .bindPopup('<?= addslashes($poi['name']) ?>')
    .openPopup();
</script>

<footer>Twin &amp; Sister Cities — UFCFV4-30-2 Data, Schemas and Applications</footer>
</body>
</html>