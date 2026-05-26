<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email inválido']);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

try {
    db()->query("INSERT INTO newsletter (email, created_at) VALUES (?, NOW())", [$email]);
    echo json_encode(['success' => true, 'message' => 'Subscrição realizada com sucesso!']);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => true, 'message' => 'Email já registado.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao subscrever.']);
    }
}
