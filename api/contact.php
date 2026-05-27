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

$name = sanitize($input['name'] ?? '');
$email = sanitize($input['email'] ?? '');
$phone = sanitize($input['phone'] ?? '');
$subject = sanitize($input['subject'] ?? '');
$message = sanitize($input['message'] ?? '');
$plan = sanitize($input['plan'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    jsonResponse(['error' => 'Nome, email e mensagem são obrigatórios'], 400);
}

if (!validateEmail($email)) {
    jsonResponse(['error' => 'Email inválido'], 400);
}

try {
    db()->insert('messages', [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject ?: 'Sem assunto',
        'message' => $message,
        'plan_name' => $plan
    ]);

    // Send WhatsApp Cloud API notification
    try {
        require_once __DIR__ . '/whatsapp.php';
        $wa = new WhatsAppCloudAPI();
        $adminWa = getSetting('whatsapp_number', '244935603163');
        $wa->sendContactNotification($adminWa, $name, $email, $phone, $subject, $message);
    } catch (Exception $e) {
        error_log("WhatsApp contact notification failed: " . $e->getMessage());
    }

    $whatsapp = getSetting('whatsapp_number', '244935603163');
    $whatsappMsg = "Olá ANGONUEVE!%0A";
    $whatsappMsg .= "*Nome:* " . urlencode($name) . "%0A";
    $whatsappMsg .= "*Email:* " . urlencode($email) . "%0A";
    $whatsappMsg .= "*Telefone:* " . urlencode($phone) . "%0A";
    $whatsappMsg .= "*Assunto:* " . urlencode($subject ?: 'Sem assunto') . "%0A";
    $whatsappMsg .= "*Mensagem:* " . urlencode($message);
    if ($plan) $whatsappMsg .= "%0A*Plano:* " . urlencode($plan);

    jsonResponse([
        'success' => true,
        'message' => 'Mensagem enviada com sucesso!',
        'whatsapp_url' => "https://wa.me/{$whatsapp}?text={$whatsappMsg}"
    ]);
} catch (Exception $e) {
    jsonResponse(['error' => 'Erro ao enviar mensagem'], 500);
}
