<?php
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['valid' => false, 'error' => 'Método não permitido']);
    exit;
}

$nif = $_POST['nif'] ?? '';
$nif = preg_replace('/[^0-9]/', '', $nif);

$valid = validateNIF($nif);

echo json_encode([
    'valid' => $valid,
    'formatted' => $valid ? formatNIF($nif) : $nif,
    'message' => $valid ? 'NIF angolano válido' : 'NIF inválido. Verifique o dígito de controlo.'
]);
