<?php
require_once 'db.php';
$pdo = getDB();

$cities = $pdo->query("SELECT * FROM city")->fetchAll();
$twinning = $pdo->query("
    SELECT t.year_established, c1.name AS city1, c2.name AS city2
    FROM twinning t
    JOIN city c1 ON t.city1_id = c1.id
    JOIN city c2 ON t.city2_id = c2.id
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Twin & Sister Cities — Birmingham</title>
    <link rel="stylesheet" href="style.css">
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
        <h2>About This Project</h2>
        <p>Exploring Birmingham and its twin cities: Frankfurt (Germany) and Lyon (France). Twin city relationships foster cultural exchange, trade, and friendship between cities around the world.</p>
    </div>

    <div class="city-grid">
        <?php foreach ($cities as $city): ?>
        <div class="city-card">
            <h3><?= htmlspecialchars($city['name']) ?>, <?= htmlspecialchars($city['country']) ?></h3>
            <p><strong>Population:</strong> <?= number_format($city['population']) ?></p>
            <p><strong>Currency:</strong> <?= htmlspecialchars($city['currency']) ?></p>
            <p><strong>Language:</strong> <?= htmlspecialchars($city['language']) ?></p>
            <p><strong>Timezone:</strong> <?= htmlspecialchars($city['timezone']) ?></p>
            <p style="margin-top:8px; color:#555; font-size:0.85rem;"><?= htmlspecialchars($city['description']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card" style="margin-top:20px;">
        <h2>Twinning Relationships</h2>
        <table>
            <tr><th>City</th><th>Twin City</th><th>Year Established</th></tr>
            <?php foreach ($twinning as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['city1']) ?></td>
                <td><?= htmlspecialchars($t['city2']) ?></td>
                <td><?= $t['year_established'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<footer>Twin &amp; Sister Cities — UFCFV4-30-2 Data, Schemas and Applications</footer>
</body>
</html>