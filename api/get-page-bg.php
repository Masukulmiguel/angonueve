<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$page = sanitize($_GET['page'] ?? '');
$allowed = ['home', 'about', 'contact', 'services', 'models', 'login', 'register',
    'slide_1', 'slide_2', 'slide_3',
    'servico_hospedagem', 'servico_dominios', 'servico_email', 'servico_criacao-sites'];

if (!in_array($page, $allowed)) {
    echo json_encode(['success' => false, 'url' => null]);
    exit;
}

$filename = getSetting('page_bg_' . $page, '');

if ($filename) {
    echo json_encode(['success' => true, 'url' => SITE_URL . '/uploads/page-bg/' . $filename]);
} else {
    echo json_encode(['success' => true, 'url' => null]);
}
