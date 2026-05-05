<?php
function getConfig() {
    $xml = simplexml_load_file(__DIR__ . '/config.xml');
    if (!$xml) {
        die('Failed to load config.xml');
    }
    return $xml;
}

$config = getConfig();

define('DB_HOST', (string)$config->database->host);
define('DB_NAME', (string)$config->database->name);
define('DB_USER', (string)$config->database->user);
define('DB_PASS', (string)$config->database->password);
define('DB_CHARSET', (string)$config->database->charset);

define('WEATHER_API', (string)$config->apis->api[0]->base_url);
define('MAP_TILE',    (string)$config->apis->api[1]->tile_url);
define('APP_NAME',    (string)$config->application->name);