<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$page = $input['page'] ?? $_SERVER['HTTP_REFERER'] ?? 'unknown';
$ua = getUserAgent();

try {
    db()->insert('visitors', [
        'ip_address' => getIP(),
        'user_agent' => $ua,
        'page_visited' => $page,
        'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
        'browser' => getBrowser($ua),
        'os' => getOS($ua),
        'device' => getDevice($ua)
    ]);
    jsonResponse(['success' => true]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => 'Tracking error'], 500);
}
