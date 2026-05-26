<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('invoices');

$user = currentUser();
$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);

if ($action === 'create' || $action === 'edit') {
    $isEdit = $action === 'edit' && $id;
    $invoice = null;
    if ($isEdit) {
        $invoice = db()->fetchOne("SELECT * FROM invoices WHERE id = ?", [$id]);
        if (!$invoice) { header('Location: invoices.php'); exit; }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_csrf();
        $data = [
            'client_name' => sanitize($_POST['client_name']),
            'client_email' => sanitize($_POST['client_email']),
            'client_phone' => sanitize($_POST['client_phone']),
            'client_address' => sanitize($_POST['client_address']),
            'service_name' => sanitize($_POST['service_name']),
            'plan_name' => sanitize($_POST['plan_name']),
            'quantity' => intval($_POST['quantity'] ?? 1),
            'unit_price' => floatval($_POST['unit_price'] ?? 0),
            'discount' => floatval($_POST['discount'] ?? 0),
            'tax' => floatval($_POST['tax'] ?? 0),
            'due_date' => sanitize($_POST['due_date']),
            'notes' => sanitize($_POST['notes']),
            'order_id' => intval($_POST['order_id'] ?? 0) ?: null,
            'client_id' => intval($_POST['client_id'] ?? 0),
        ];
        $data['description'] = sanitize($_POST['description'] ?? $data['service_name']);
        $subtotal = $data['unit_price'] * $data['quantity'];
        $data['total'] = $subtotal - $data['discount'] + $data['tax'];

        if (empty($data['client_name']) || empty($data['client_email']) || $data['unit_price'] <= 0) {
            $error = 'Preencha todos os campos obrigatórios (nome, email, preço)';
        } elseif ($data['client_id'] <= 0) {
            $error = 'Selecione um cliente válido';
        } else {
            if ($isEdit) {
                db()->update('invoices', $data, 'id = :id', ['id' => $id]);
                logActivity($user['id'], 'update', "Factura #{$id} actualizada");
                header('Location: invoices.php?msg=updated');
                exit;
            } else {
                $data['invoice_no'] = generateInvoiceNo();
                $data['created_by'] = $user['id'];
                db()->insert('invoices', $data);
                logActivity($user['id'], 'create', "Factura {$data['invoice_no']} criada");
                try {
                    $invLink = SITE_URL . '/admin/invoice-pdf.php?id=' . db()->lastInsertId();
                    $html = emailTemplateInvoiceCreated($data['client_name'], $data['invoice_no'], $data['total'], $invLink);
                    sendEmail($data['client_email'], 'Factura Criada - ' . $data['invoice_no'], $html);
                } catch (Exception $e) {
                    error_log("Email invoice notification failed: " . $e->getMessage());
                }
                header('Location: invoices.php?msg=created');
                exit;
            }
        }
    }

    $clients = db()->fetchAll("SELECT id, name, email, phone FROM users WHERE role = 'client' AND status = 'active' ORDER BY name ASC");
    $orders = db()->fetchAll("SELECT id, customer_name, service_name, plan_name, price_monthly FROM orders ORDER BY created_at DESC LIMIT 50");
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $isEdit ? 'Editar' : 'Nova' ?> Factura - ANGONUEVE CRM</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/admin.css">
        <style>
            .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
            .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
            .form-group { margin-bottom: 16px; }
            .form-group label { display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; font-weight: 500; }
            .form-group input, .form-group select, .form-group textarea {
                width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px;
                background: rgba(255,255,255,0.03); color: var(--text); font-family: 'Inter', sans-serif;
                font-size: 0.9rem; outline: none; transition: all 0.3s;
            }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--primary); }
            .form-group textarea { min-height: 80px; resize: vertical; }
            select option { background: #0d1f3c; }
        </style>
    </head>
    <body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-file-invoice"></i> <span><?= $isEdit ? 'Editar' : 'Nova' ?> Factura</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <div class="detail-card">
                    <form method="POST">
                        <?= csrf_field() ?>
                        <div class="company-info" style="margin-bottom:24px;padding:16px 20px;background:rgba(0,212,255,0.04);border:1px solid rgba(0,212,255,0.15);border-radius:10px;">
                            <h3 style="margin:0 0 8px;font-size:0.85rem;color:var(--primary);"><i class="fas fa-building"></i> Emitente (da empresa)</h3>
                            <div style="display:grid;grid-template-columns:auto 1fr;gap:2px 16px;font-size:0.82rem;color:var(--text-muted);">
                                <span>Empresa:</span><span style="color:var(--text);font-weight:600;"><?= sanitize(getSetting('site_name', 'ANGONUEVE')) ?></span>
                                <span>NIF:</span><span style="color:var(--text);"><?= sanitize(getSetting('bank_nif', '5000000000')) ?></span>
                                <span>Email:</span><span style="color:var(--text);"><?= sanitize(getSetting('site_email', 'geral@angonueve.co')) ?></span>
                                <span>Telefone:</span><span style="color:var(--text);"><?= sanitize(getSetting('site_phone', '935 603 163')) ?></span>
                                <span>Endereço:</span><span style="color:var(--text);"><?= nl2br(sanitize(getSetting('site_address', 'Luanda, Angola'))) ?></span>
                            </div>
                            <small style="display:block;margin-top:6px;font-size:0.72rem;color:var(--text-muted);">Gerido em <a href="settings.php" style="color:var(--primary);">Configurações</a></small>
                        </div>
                        <h3 style="margin-bottom:20px;font-size:1rem;color:var(--primary);"><i class="fas fa-user"></i> Dados do Cliente</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Cliente *</label>
                                <select name="client_id" id="clientSelect" required onchange="fillClientData()">
                                    <option value="">Selecionar cliente...</option>
                                    <?php foreach ($clients as $c): ?>
                                        <option value="<?= $c['id'] ?>" 
                                            data-name="<?= sanitize($c['name']) ?>"
                                            data-email="<?= sanitize($c['email']) ?>"
                                            data-phone="<?= sanitize($c['phone']) ?>"
                                            <?= ($isEdit && $invoice['client_id'] == $c['id']) ? 'selected' : '' ?>>
                                            <?= sanitize($c['name']) ?> (<?= sanitize($c['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Encomenda (opcional)</label>
                                <select name="order_id" id="orderSelect" onchange="fillOrderData()">
                                    <option value="">Nenhuma</option>
                                    <?php foreach ($orders as $o): ?>
                                        <option value="<?= $o['id'] ?>" 
                                            data-service="<?= sanitize($o['service_name']) ?>"
                                            data-plan="<?= sanitize($o['plan_name']) ?>"
                                            data-price="<?= $o['price_monthly'] ?>"
                                            data-client="<?= sanitize($o['customer_name']) ?>"
                                            data-email="<?= sanitize($o['customer_email']) ?>"
                                            <?= ($isEdit && $invoice['order_id'] == $o['id']) ? 'selected' : '' ?>>
                                            #<?= $o['id'] ?> - <?= sanitize($o['customer_name']) ?> - <?= sanitize($o['service_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nome *</label>
                                <input type="text" name="client_name" id="clientName" value="<?= $isEdit ? sanitize($invoice['client_name']) : '' ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="client_email" id="clientEmail" value="<?= $isEdit ? sanitize($invoice['client_email']) : '' ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Telefone</label>
                                <input type="text" name="client_phone" id="clientPhone" value="<?= $isEdit ? sanitize($invoice['client_phone'] ?? '') : '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Endereço</label>
                                <input type="text" name="client_address" id="clientAddress" value="<?= $isEdit ? sanitize($invoice['client_address'] ?? '') : '' ?>">
                            </div>
                        </div>

                        <h3 style="margin:24px 0 20px;font-size:1rem;color:var(--primary);"><i class="fas fa-cogs"></i> Detalhes do Serviço</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Serviço</label>
                                <input type="text" name="service_name" id="serviceName" value="<?= $isEdit ? sanitize($invoice['service_name'] ?? '') : '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Plano</label>
                                <input type="text" name="plan_name" id="planName" value="<?= $isEdit ? sanitize($invoice['plan_name'] ?? '') : '' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea name="description"><?= $isEdit ? sanitize($invoice['description'] ?? '') : '' ?></textarea>
                        </div>

                        <h3 style="margin:24px 0 20px;font-size:1rem;color:var(--primary);"><i class="fas fa-calculator"></i> Valores</h3>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <input type="number" name="quantity" id="quantity" value="<?= $isEdit ? $invoice['quantity'] : '1' ?>" min="1" onchange="calcTotal()">
                            </div>
                            <div class="form-group">
                                <label>Preço Unitário (Kz) *</label>
                                <input type="number" name="unit_price" id="unitPrice" value="<?= $isEdit ? $invoice['unit_price'] : '' ?>" step="0.01" min="0" required onchange="calcTotal()">
                            </div>
                            <div class="form-group">
                                <label>Data de Vencimento</label>
                                <input type="date" name="due_date" value="<?= $isEdit ? $invoice['due_date'] : date('Y-m-d', strtotime('+30 days')) ?>">
                            </div>
                        </div>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label>Desconto (Kz)</label>
                                <input type="number" name="discount" id="discount" value="<?= $isEdit ? $invoice['discount'] : '0' ?>" step="0.01" min="0" onchange="calcTotal()">
                            </div>
                            <div class="form-group">
                                <label>Taxa/IVA (Kz)</label>
                                <input type="number" name="tax" id="tax" value="<?= $isEdit ? $invoice['tax'] : '0' ?>" step="0.01" min="0" onchange="calcTotal()">
                            </div>
                            <div class="form-group">
                                <label>Total: <strong id="totalDisplay" style="color:var(--primary);font-size:1.2rem;">Kz 0,00</strong></label>
                                <input type="hidden" name="total" id="totalInput" value="<?= $isEdit ? $invoice['total'] : '0' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Notas Internas</label>
                            <textarea name="notes"><?= $isEdit ? sanitize($invoice['notes'] ?? '') : '' ?></textarea>
                        </div>
                        <div style="display:flex;gap:12px;margin-top:24px;padding-top:24px;border-top:1px solid var(--border);">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar' : 'Criar' ?> Factura</button>
                            <a href="invoices.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
    function fillClientData() {
        const sel = document.getElementById('clientSelect');
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('clientName').value = opt.dataset.name || '';
            document.getElementById('clientEmail').value = opt.dataset.email || '';
            document.getElementById('clientPhone').value = opt.dataset.phone || '';
        }
    }
    function fillOrderData() {
        const sel = document.getElementById('orderSelect');
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('serviceName').value = opt.dataset.service || '';
            document.getElementById('planName').value = opt.dataset.plan || '';
            document.getElementById('unitPrice').value = opt.dataset.price || '0';
            if (!document.getElementById('clientName').value) {
                document.getElementById('clientName').value = opt.dataset.client || '';
                document.getElementById('clientEmail').value = opt.dataset.email || '';
            }
            calcTotal();
        }
    }
    function calcTotal() {
        const qty = parseFloat(document.getElementById('quantity').value) || 1;
        const price = parseFloat(document.getElementById('unitPrice').value) || 0;
        const disc = parseFloat(document.getElementById('discount').value) || 0;
        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const total = (qty * price) - disc + tax;
        document.getElementById('totalDisplay').textContent = 'Kz ' + total.toLocaleString('pt-PT', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('totalInput').value = total.toFixed(2);
    }
    calcTotal();
    </script>
    </body>
    </html>
    <?php
    exit;
}

if ($action === 'delete' && $id) {
    if (isAdmin()) {
        $inv = db()->fetchOne("SELECT invoice_no FROM invoices WHERE id = ?", [$id]);
        db()->delete('invoices', 'id = ?', [$id]);
        logActivity($user['id'], 'delete', "Factura {$inv['invoice_no']} eliminada");
        header('Location: invoices.php?msg=deleted');
        exit;
    } else {
        die('Apenas administradores podem eliminar');
    }
}

if ($action === 'cancel' && $id) {
    db()->update('invoices', ['status' => 'cancelled'], 'id = :id', ['id' => $id]);
    logActivity($user['id'], 'cancel', "Factura #{$id} cancelada");
    header('Location: invoices.php?msg=cancelled');
    exit;
}

$page = max(1, intval($_GET['page'] ?? 1));
$status = sanitize($_GET['status'] ?? '');
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;
$where = '';
$params = [];
if ($status) { $where = 'WHERE status = ?'; $params[] = $status; }
$total = db()->count('invoices', $where, $params);
$invoices = db()->fetchAll("SELECT * FROM invoices {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?", array_merge($params, [$perPage, $offset]));
$msg = sanitize($_GET['msg'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturas - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-file-invoice"></i> <span>Facturas</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content">
            <?php if ($msg === 'created'): ?><div class="alert alert-success">Factura criada com sucesso!</div><?php endif; ?>
            <?php if ($msg === 'updated'): ?><div class="alert alert-success">Factura actualizada com sucesso!</div><?php endif; ?>
            <?php if ($msg === 'deleted'): ?><div class="alert alert-success">Factura eliminada!</div><?php endif; ?>
            <?php if ($msg === 'cancelled'): ?><div class="alert alert-success">Factura cancelada!</div><?php endif; ?>

            <div class="table-controls">
                <div class="filter-tabs">
                    <a href="?status=" class="btn-sm <?= !$status ? 'active' : '' ?>">Todas</a>
                    <a href="?status=pending" class="btn-sm <?= $status === 'pending' ? 'active' : '' ?>">Pendentes</a>
                    <a href="?status=paid" class="btn-sm <?= $status === 'paid' ? 'active' : '' ?>">Pagas</a>
                    <a href="?status=cancelled" class="btn-sm <?= $status === 'cancelled' ? 'active' : '' ?>">Canceladas</a>
                </div>
                <a href="?action=create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nova Factura</a>
            </div>
            <div class="table-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invoices)): ?>
                            <tr><td colspan="8" class="empty-state">Nenhuma factura encontrada</td></tr>
                        <?php else: ?>
                            <?php foreach ($invoices as $inv): ?>
                                <tr>
                                    <td><strong><?= sanitize($inv['invoice_no']) ?></strong></td>
                                    <td><?= sanitize($inv['client_name']) ?></td>
                                    <td><?= sanitize($inv['service_name'] ?? '-') ?></td>
                                    <td>Kz <?= number_format($inv['total'], 0, ',', ' ') ?></td>
                                    <td><?= $inv['due_date'] ? formatDate($inv['due_date'], 'd/m/Y') : '-' ?></td>
                                    <td><?= statusBadge($inv['status']) ?></td>
                                    <td><?= formatDate($inv['created_at'], 'd/m/Y') ?></td>
                                    <td class="actions">
                                        <a href="invoice-pdf.php?id=<?= $inv['id'] ?>" class="btn-icon" title="PDF" target="_blank"><i class="fas fa-file-pdf"></i></a>
                                        <a href="?action=edit&id=<?= $inv['id'] ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                        <?php if ($inv['status'] === 'pending'): ?>
                                            <a href="?action=cancel&id=<?= $inv['id'] ?>" class="btn-icon danger" title="Cancelar" onclick="return confirm('Cancelar factura?')"><i class="fas fa-ban"></i></a>
                                        <?php endif; ?>
                                        <a href="?action=delete&id=<?= $inv['id'] ?>" class="btn-icon danger" title="Eliminar" onclick="return confirm('Eliminar factura?')"><i class="fas fa-trash"></i></a>
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
