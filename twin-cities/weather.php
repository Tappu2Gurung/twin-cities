<?php
require_once 'db.php';
$pdo = getDB();
$cities = $pdo->query("SELECT * FROM city")->fetchAll();

function getWeather($lat, $lon) {
    $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}"
         . "&current=temperature_2m,weathercode,windspeed_10m,relative_humidity_2m"
         . "&daily=temperature_2m_max,temperature_2m_min,weathercode"
         . "&timezone=auto&forecast_days=5";
    $response = @file_get_contents($url);
    return $response ? json_decode($response, true) : null;
}

function weatherDescription($code) {
    $codes = [
        0=>'Clear sky', 1=>'Mainly clear', 2=>'Partly cloudy', 3=>'Overcast',
        45=>'Foggy', 48=>'Icy fog', 51=>'Light drizzle', 53=>'Drizzle',
        55=>'Heavy drizzle', 61=>'Light rain', 63=>'Rain', 65=>'Heavy rain',
        71=>'Light snow', 73=>'Snow', 75=>'Heavy snow', 80=>'Rain showers',
        81=>'Rain showers', 82=>'Violent showers', 85=>'Snow showers',
        95=>'Thunderstorm', 96=>'Thunderstorm w/ hail', 99=>'Thunderstorm w/ hail'
    ];
    return $codes[$code] ?? 'Unknown';
}

function weatherEmoji($code) {
    if ($code == 0 || $code == 1) return '☀️';
    if ($code == 2 || $code == 3) return '⛅';
    if ($code >= 51 && $code <= 67) return '🌧️';
    if ($code >= 71 && $code <= 77) return '❄️';
    if ($code >= 80 && $code <= 82) return '🌦️';
    if ($code >= 95) return '⛈️';
    return '🌤️';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weather — Twin Cities</title>
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
        <h2>Current &amp; Forecast Weather</h2>
        <p style="color:#666; font-size:0.9rem;">Data provided by Open-Meteo (free, no API key required)</p>
    </div>

    <div class="weather-grid">
        <?php foreach ($cities as $city):
            $w = getWeather($city['latitude'], $city['longitude']);
            if (!$w) continue;
            $current = $w['current'];
            $daily   = $w['daily'];
        ?>
        <div class="weather-card">
            <h3><?= htmlspecialchars($city['name']) ?></h3>
            <div style="font-size:2.5rem;"><?= weatherEmoji($current['weathercode']) ?></div>
            <div class="temp"><?= round($current['temperature_2m']) ?>°C</div>
            <p><?= weatherDescription($current['weathercode']) ?></p>
            <p style="font-size:0.85rem; color:#666; margin-top:6px;">
                💨 <?= $current['windspeed_10m'] ?> km/h &nbsp;|&nbsp;
                💧 <?= $current['relative_humidity_2m'] ?>%
            </p>

            <div class="forecast">
                <?php for ($i = 1; $i < 5; $i++):
                    $date = date('D', strtotime($daily['time'][$i]));
                ?>
                <div class="forecast-day">
                    <div><?= $date ?></div>
                    <div style="font-size:1.2rem;"><?= weatherEmoji($daily['weathercode'][$i]) ?></div>
                    <div class="day-temp"><?= round($daily['temperature_2m_max'][$i]) ?>°</div>
                    <div style="color:#999;"><?= round($daily['temperature_2m_min'][$i]) ?>°</div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<footer>Twin &amp; Sister Cities — UFCFV4-30-2 Data, Schemas and Applications</footer>
</body>
</html>