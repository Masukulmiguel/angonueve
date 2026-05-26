<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

header('Content-Type: application/json');

$user = currentUser();
$input = json_decode(file_get_contents('php://input'), true);
$siteId = intval($input['site_id'] ?? 0);

if (!$siteId) {
    jsonResponse(['error' => 'ID do site inválido'], 400);
}

// Verify the site belongs to this user
$site = db()->fetchOne("SELECT id, status FROM generated_sites WHERE id = ? AND user_id = ?", [$siteId, $user['id']]);
if (!$site) {
    jsonResponse(['error' => 'Site não encontrado'], 404);
}

if ($site['status'] === 'paid') {
    jsonResponse(['success' => true, 'already_paid' => true]);
}

if ($site['status'] === 'pending_payment') {
    // Check if there's already an unpaid invoice
    $existing = db()->fetchOne(
        "SELECT i.id FROM invoices i JOIN generated_sites g ON i.id = g.invoice_id WHERE g.id = ? AND i.status = 'pending'",
        [$siteId]
    );
    if ($existing) {
        jsonResponse(['success' => true, 'invoice_id' => $existing['id']]);
    }
}

$price = floatval(getSetting('ai_site_price', '15000'));
$invoiceNo = generateInvoiceNo();

try {
    db()->beginTransaction();

    db()->insert('invoices', [
        'invoice_no' => $invoiceNo,
        'client_id' => $user['id'],
        'client_name' => $user['name'],
        'client_email' => $user['email'],
        'service_name' => 'Criação de Site com IA',
        'description' => 'Site gerado por inteligência artificial - #' . $siteId,
        'unit_price' => $price,
        'total' => $price,
        'status' => 'pending',
        'created_by' => $user['id']
    ]);
    $invoiceId = db()->lastInsertId();

    db()->update('generated_sites', [
        'status' => 'pending_payment',
        'invoice_id' => $invoiceId
    ], 'id = :id', ['id' => $siteId]);

    db()->commit();

    jsonResponse(['success' => true, 'invoice_id' => $invoiceId, 'invoice_no' => $invoiceNo, 'price' => $price]);
} catch (Exception $e) {
    db()->rollback();
    jsonResponse(['error' => 'Erro ao criar fatura'], 500);
}
