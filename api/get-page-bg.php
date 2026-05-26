<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$page = sanitize($_GET['page'] ?? '');
$allowed = ['home', 'about', 'contact', 'services', 'models', 'login', 'register'];

if (!in_array($page, $allowed)) {
    echo json_encode(['success' => false, 'url' => null]);
    exit;
}

$key = 'page_bg_' . $page;

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['setting_value']) {
        $url = SITE_URL . '/uploads/page-bg/' . $row['setting_value'];
        echo json_encode(['success' => true, 'url' => $url]);
    } else {
        echo json_encode(['success' => true, 'url' => null]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'url' => null]);
}
