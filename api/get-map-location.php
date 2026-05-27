<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$settings = [];
$rows = db()->fetchAll("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('map_latitude','map_longitude','map_address','map_zoom')");
foreach ($rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

echo json_encode([
    'lat'  => $settings['map_latitude'] ?? '-8.838333',
    'lng'  => $settings['map_longitude'] ?? '13.234444',
    'addr' => $settings['map_address'] ?? 'Luanda, Angola',
    'zoom' => (int)($settings['map_zoom'] ?? 13),
]);
