<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('payments');

$user = currentUser();

$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);

if ($action === 'view' && $id) {
    $payment = db()->fetchOne("SELECT * FROM payments WHERE id = ?", [$id]);
    if (!$payment) { header('Location: payments.php'); exit; }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_csrf();
        $newStatus = sanitize($_POST['status'] ?? '');
        $notes = sanitize($_POST['admin_notes'] ?? '');

        if (in_array($newStatus, ['confirmed', 'rejected'])) {
            $updateData = ['status' => $newStatus, 'admin_notes' => $notes, 'confirmed_by' => $user['id']];
            if ($newStatus === 'confirmed') {
                $updateData['confirmed_at'] = date('Y-m-d H:i:s');
            } elseif ($newStatus === 'rejected') {
                $updateData['rejected_at'] = date('Y-m-d H:i:s');
            }
            db()->update('payments', $updateData, 'id = :id', ['id' => $id]);

            if ($newStatus === 'confirmed') {
                db()->update('invoices', ['status' => 'paid', 'paid_at' => date('Y-m-d H:i:s'), 'payment_method' => $payment['method']], 'id = :id', ['id' => $payment['invoice_id']]);

                $inv = db()->fetchOne("SELECT * FROM invoices WHERE id = ?", [$payment['invoice_id']]);
                if ($inv && $inv['service_name']) {
                    activateClientService($payment['client_id'], 'service_' . $inv['id'], $inv['service_name'], $inv['plan_name'], $inv['id']);
                }
                logActivity($user['id'], 'confirm', "Pagamento #{$id} confirmado - Factura {$payment['invoice_no']}");
                try {
                    $html = emailTemplatePaymentConfirmed($payment['client_name'], $payment['invoice_no'], $payment['amount']);
                    sendEmail($payment['client_email'], 'Pagamento Confirmado - ' . $payment['invoice_no'], $html);
                } catch (Exception $e) {
                    error_log("Email payment confirmed notification failed: " . $e->getMessage());
                }
            } else {
                logActivity($user['id'], 'reject', "Pagamento #{$id} rejeitado - Factura {$payment['invoice_no']}");
                try {
                    $html = emailTemplatePaymentRejected($payment['client_name'], $payment['invoice_no'], $notes);
                    sendEmail($payment['client_email'], 'Pagamento Rejeitado - ' . $payment['invoice_no'], $html);
                } catch (Exception $e) {
                    error_log("Email payment rejected notification failed: " . $e->getMessage());
                }
            }
            echo '<meta http-equiv="refresh" content="0">';
        }
    }

    $inv = db()->fetchOne("SELECT * FROM invoices WHERE id = ?", [$payment['invoice_id']]);
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pagamento #<?= $id ?> - ANGONUEVE CRM</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/admin.css">
        <style>
            .proof-preview { max-width: 400px; border-radius: 8px; border: 1px solid var(--border); margin-top: 12px; }
            .proof-preview img { max-width: 100%; border-radius: 8px; }
        </style>
    </head>
    <body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-credit-card"></i> <span>Pagamento #<?= $id ?></span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <div class="detail-card">
                    <div class="detail-header">
                        <h2>Pagamento #<?= $id ?> — <?= sanitize($payment['invoice_no']) ?></h2>
                        <?= statusBadge($payment['status']) ?>
                    </div>
                    <div class="detail-meta" style="grid-template-columns:repeat(3,1fr);">
                        <p><i class="fas fa-user"></i> <?= sanitize($payment['client_name']) ?></p>
                        <p><i class="fas fa-file-invoice"></i> Factura: <?= sanitize($payment['invoice_no']) ?></p>
                        <p><i class="fas fa-money-bill"></i> Kz <?= number_format($payment['amount'], 0, ',', ' ') ?></p>
                        <p><i class="fas fa-credit-card"></i> Método: <?= strtoupper($payment['method']) ?></p>
                        <p><i class="fas fa-hashtag"></i> Referência: <?= sanitize($payment['reference'] ?: 'N/A') ?></p>
                        <p><i class="fas fa-clock"></i> <?= formatDate($payment['created_at']) ?></p>
                    </div>

                    <?php if ($payment['proof_file']): ?>
                        <div style="margin-bottom:24px;">
                            <h4 style="margin-bottom:8px;color:var(--text-muted);">Comprovativo de Pagamento</h4>
                            <div class="proof-preview">
                                <?php
                                $proofUrl = '../uploads/proofs/' . $payment['proof_file'];
                                $ext = strtolower(pathinfo($payment['proof_file'], PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="<?= $proofUrl ?>" alt="Comprovativo" style="max-width:100%;border-radius:8px;">
                                <?php else: ?>
                                    <a href="<?= $proofUrl ?>" target="_blank" class="btn btn-primary"><i class="fas fa-file-pdf"></i> Ver Comprovativo (PDF)</a>
                                <?php endif; ?>
                            </div>
                            <small style="color:var(--text-muted);display:block;margin-top:4px;"><?= sanitize($payment['proof_original_name']) ?></small>
                        </div>
                    <?php endif; ?>

                    <?php if ($payment['status'] === 'pending'): ?>
                        <form method="POST" style="padding-top:20px;border-top:1px solid var(--border);">
                            <?= csrf_field() ?>
                            <h4 style="margin-bottom:12px;color:var(--text-muted);">Confirmar ou Rejeitar Pagamento</h4>
                            <div style="display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                                <select name="status" style="padding:10px 14px;border:1px solid var(--border);border-radius:8px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;">
                                    <option value="confirmed">Confirmar Pagamento</option>
                                    <option value="rejected">Rejeitar Pagamento</option>
                                </select>
                                <input type="text" name="admin_notes" placeholder="Notas (opcional)" style="padding:10px 14px;border:1px solid var(--border);border-radius:8px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;min-width:250px;">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Actualizar</button>
                            </div>
                        </form>
                    <?php elseif ($payment['admin_notes']): ?>
                        <div style="padding-top:16px;border-top:1px solid var(--border);">
                            <h4 style="margin-bottom:8px;color:var(--text-muted);">Notas do Admin</h4>
                            <p style="color:var(--text);padding:12px 16px;background:rgba(255,255,255,0.03);border-radius:8px;"><?= nl2br(sanitize($payment['admin_notes'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($payment['confirmed_by']): ?>
                        <p style="margin-top:16px;font-size:0.85rem;color:var(--text-muted);">
                            <i class="fas fa-user-check"></i> 
                            <?= $payment['status'] === 'confirmed' ? 'Confirmado' : 'Rejeitado' ?> 
                            em <?= formatDate($payment['confirmed_at'] ?: $payment['rejected_at']) ?>
                        </p>
                    <?php endif; ?>

                    <div class="detail-actions">
                        <a href="payments.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                        <?php if ($inv): ?>
                            <a href="invoice-pdf.php?id=<?= $inv['id'] ?>" class="btn btn-primary" target="_blank"><i class="fas fa-file-invoice"></i> Ver Factura</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    </body>
    </html>
    <?php
    exit;
}

if ($action === 'delete' && $id) {
    if (isAdmin()) {
        db()->delete('payments', 'id = ?', [$id]);
        logActivity($user['id'], 'delete', "Pagamento #{$id} eliminado");
        header('Location: payments.php?msg=deleted');
        exit;
    } else {
        die('Apenas administradores podem eliminar');
    }
}

$page = max(1, intval($_GET['page'] ?? 1));
$status = sanitize($_GET['status'] ?? '');
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;
$where = '';
$params = [];
if ($status) { $where = 'WHERE status = ?'; $params[] = $status; }
$total = db()->count('payments', $where, $params);
$payments = db()->fetchAll("SELECT * FROM payments {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?", array_merge($params, [$perPage, $offset]));
$msg = sanitize($_GET['msg'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamentos - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-credit-card"></i> <span>Pagamentos</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content">
            <?php if ($msg === 'deleted'): ?><div class="alert alert-success">Pagamento eliminado!</div><?php endif; ?>
            <div class="table-controls">
                <div class="filter-tabs">
                    <a href="?status=" class="btn-sm <?= !$status ? 'active' : '' ?>">Todos</a>
                    <a href="?status=pending" class="btn-sm <?= $status === 'pending' ? 'active' : '' ?>">Pendentes</a>
                    <a href="?status=confirmed" class="btn-sm <?= $status === 'confirmed' ? 'active' : '' ?>">Confirmados</a>
                    <a href="?status=rejected" class="btn-sm <?= $status === 'rejected' ? 'active' : '' ?>">Rejeitados</a>
                </div>
            </div>
            <div class="table-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Comprovativo</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr><td colspan="9" class="empty-state">Nenhum pagamento encontrado</td></tr>
                        <?php else: ?>
                            <?php foreach ($payments as $p): ?>
                                <tr>
                                    <td>#<?= $p['id'] ?></td>
                                    <td><?= sanitize($p['invoice_no']) ?></td>
                                    <td><?= sanitize($p['client_name']) ?></td>
                                    <td>Kz <?= number_format($p['amount'], 0, ',', ' ') ?></td>
                                    <td><span class="badge badge-info"><?= strtoupper($p['method']) ?></span></td>
                                    <td>
                                        <?php if ($p['proof_file']): ?>
                                            <a href="../uploads/proofs/<?= urlencode($p['proof_file']) ?>" target="_blank" class="btn-icon" title="Ver comprovativo"><i class="fas fa-paperclip"></i></a>
                                        <?php else: ?>
                                            <span style="color:var(--text-muted);font-size:0.8rem;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= statusBadge($p['status']) ?></td>
                                    <td><?= formatDate($p['created_at'], 'd/m/Y') ?></td>
                                    <td class="actions">
                                        <a href="?action=view&id=<?= $p['id'] ?>" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="?action=delete&id=<?= $p['id'] ?>" class="btn-icon danger" title="Eliminar" onclick="return confirm('Eliminar pagamento?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if ($total > $perPage): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                            <a href="?page=<?= $i ?>&status=<?= $status ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
