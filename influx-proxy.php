<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$influxUrl = 'http://10.0.0.81:8086';
$database = 'iobroker';

if (!isset($_GET['action'])) {
    echo json_encode(['status' => 'proxy ready']);
    exit;
}

$action = $_GET['action'];
$period = isset($_GET['period']) ? $_GET['period'] : 'day';

// Zeitraum festlegen
if ($period == 'day') {
    $timeFilter = 'time >= now() - 1d';
} elseif ($period == 'year') {
    $timeFilter = 'time >= now() - 365d';
} else {
    $timeFilter = 'time >= now() - 1d';
}

// Aktueller Wert
if ($action == 'current') {
    $query = 'SELECT last("value") FROM "Gesamtleistung"';
}
// Eingespeist (negative Werte)
elseif ($action == 'feedin') {
    $query = "SELECT integral(\"value\") / -3600000 FROM \"Gesamtleistung\" WHERE $timeFilter AND \"value\" < 0";
}
// Bezogen (positive Werte)
elseif ($action == 'consumed') {
    $query = "SELECT integral(\"value\") / 3600000 FROM \"Gesamtleistung\" WHERE $timeFilter AND \"value\" > 0";
}
// 12-Stunden Verlauf (gruppiert nach 30 Minuten)
elseif ($action == 'history12h') {
    $query = "SELECT mean(\"value\") FROM \"Gesamtleistung\" WHERE time >= now() - 12h GROUP BY time(30m) fill(null)";
}
else {
    echo json_encode(['error' => 'unknown action']);
    exit;
}

$url = $influxUrl . '/query?db=' . urlencode($database) . '&q=' . urlencode($query);
$result = @file_get_contents($url);

if ($result === false) {
    echo json_encode(['error' => 'InfluxDB not reachable']);
} else {
    echo $result;
}
?>
