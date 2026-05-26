<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: invoices.php');
    exit;
}

require_csrf();

$user = currentUser();
$invoiceId = intval($_POST['invoice_id'] ?? 0);
$method = sanitize($_POST['method'] ?? '');
$reference = sanitize($_POST['reference'] ?? '');

$inv = db()->fetchOne("SELECT * FROM invoices WHERE id = ? AND client_id = ? AND status = 'pending'", [$invoiceId, $user['id']]);
if (!$inv) {
    header("Location: invoice-view.php?id={$invoiceId}&error=" . urlencode('Factura não encontrada ou já paga'));
    exit;
}

$validMethods = ['express', 'iban', 'referencia'];
if (!in_array($method, $validMethods)) {
    header("Location: invoice-view.php?id={$invoiceId}&pay=1&error=" . urlencode('Selecione um método de pagamento'));
    exit;
}

$proofFile = $_FILES['proof_file'] ?? null;
$proofFilename = null;
$proofOriginal = null;

if ($proofFile && $proofFile['error'] !== UPLOAD_ERR_NO_FILE && $proofFile['size'] > 0) {
    $uploadResult = uploadProofFile($proofFile);
    if (isset($uploadResult['error'])) {
        header("Location: invoice-view.php?id={$invoiceId}&pay=1&error=" . urlencode($uploadResult['error']));
        exit;
    }
    $proofFilename = $uploadResult['filename'];
    $proofOriginal = $uploadResult['original'];
}

db()->insert('payments', [
    'invoice_id' => $inv['id'],
    'invoice_no' => $inv['invoice_no'],
    'client_id' => $user['id'],
    'client_name' => $user['name'],
    'amount' => $inv['total'],
    'method' => $method,
    'reference' => $reference,
    'proof_file' => $proofFilename,
    'proof_original_name' => $proofOriginal,
    'status' => 'pending'
]);

logActivity($user['id'], 'make_payment', "Pagamento para factura {$inv['invoice_no']} via {$method}");

header("Location: invoice-view.php?id={$invoiceId}&pay=1&success=1");
exit;
