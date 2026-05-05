<?php
require_once 'db.php';
$pdo = getDB();

$cities = $pdo->query("SELECT * FROM city")->fetchAll();
$pois   = $pdo->query("
    SELECT p.*, c.name AS city_name, cat.name AS category
    FROM place_of_interest p
    JOIN city c ON p.city_id = c.id
    JOIN category cat ON p.category_id = cat.id
    ORDER BY p.city_id, p.id
")->fetchAll();

header('Content-Type: application/rss+xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
    <title>Twin &amp; Sister Cities — Birmingham</title>
    <link>http://localhost/twin-cities/index.php</link>
    <description>Data about Birmingham and its twin cities Frankfurt and Lyon, including places of interest.</description>
    <language>en-gb</language>
    <lastBuildDate><?= date('r') ?></lastBuildDate>

    <?php foreach ($cities as $city): ?>
    <item>
        <title>City: <?= htmlspecialchars($city['name']) ?>, <?= htmlspecialchars($city['country']) ?></title>
        <link>http://localhost/twin-cities/index.php</link>
        <guid>city-<?= $city['id'] ?></guid>
        <description><![CDATA[
            <strong>Population:</strong> <?= number_format($city['population']) ?><br>
            <strong>Currency:</strong> <?= htmlspecialchars($city['currency']) ?><br>
            <strong>Language:</strong> <?= htmlspecialchars($city['language']) ?><br>
            <strong>Timezone:</strong> <?= htmlspecialchars($city['timezone']) ?><br>
            <strong>Coordinates:</strong> <?= $city['latitude'] ?>, <?= $city['longitude'] ?><br><br>
            <?= htmlspecialchars($city['description']) ?>
        ]]></description>
        <dc:creator>Twin Cities App</dc:creator>
    </item>
    <?php endforeach; ?>

    <?php foreach ($pois as $poi): ?>
    <item>
        <title><?= htmlspecialchars($poi['name']) ?> (<?= htmlspecialchars($poi['city_name']) ?>)</title>
        <link>http://localhost/twin-cities/poi.php?id=<?= $poi['id'] ?></link>
        <guid>poi-<?= $poi['id'] ?></guid>
        <description><![CDATA[
            <strong>City:</strong> <?= htmlspecialchars($poi['city_name']) ?><br>
            <strong>Category:</strong> <?= htmlspecialchars($poi['category']) ?><br>
            <?php if ($poi['capacity']): ?>
            <strong>Capacity:</strong> <?= number_format($poi['capacity']) ?><br>
            <?php endif; ?>
            <strong>Coordinates:</strong> <?= $poi['latitude'] ?>, <?= $poi['longitude'] ?><br><br>
            <?= htmlspecialchars($poi['description']) ?>
            <?php if ($poi['photo_url']): ?>
            <br><img src="<?= htmlspecialchars($poi['photo_url']) ?>" alt="<?= htmlspecialchars($poi['name']) ?>">
            <?php endif; ?>
        ]]></description>
        <dc:creator>Twin Cities App</dc:creator>
    </item>
    <?php endforeach; ?>

</channel>
</rss>  