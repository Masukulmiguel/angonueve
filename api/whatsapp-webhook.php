<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../api/whatsapp.php';

$wa = new WhatsAppCloudAPI();
$verifyToken = getSetting('whatsapp_webhook_verify_token', '');

// Webhook verification (GET) - Meta sends hub_mode, hub_verify_token, hub_challenge
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mode = $_GET['hub_mode'] ?? '';
    $token = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? '';

    if ($mode === 'subscribe' && $token === $verifyToken && $challenge) {
        header('Content-Type: text/plain');
        echo $challenge;
        exit;
    }

    http_response_code(403);
    echo 'Verification failed';
    exit;
}

// Incoming messages (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !$wa->verifyWebhook($input)) {
        http_response_code(200);
        echo 'EVENT_RECEIVED';
        exit;
    }

    $wa->processWebhook($input);

    http_response_code(200);
    echo 'EVENT_RECEIVED';
    exit;
}

http_response_code(405);
echo 'Method not allowed';
