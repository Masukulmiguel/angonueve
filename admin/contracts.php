<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('contracts');

$user = currentUser();
$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);

function generateContractNo() {
    $prefix = getSetting('contract_prefix', 'CTR-');
    $nextNum = intval(getSetting('contract_next_number', '1001'));
    $contractNo = $prefix . $nextNum;
    updateSetting('contract_next_number', $nextNum + 1);
    return $contractNo;
}

if ($action === 'create' || $action === 'edit') {
    $isEdit = $action === 'edit' && $id;
    $contract = null;
    if ($isEdit) {
        $contract = db()->fetchOne("SELECT * FROM contracts WHERE id = ?", [$id]);
        if (!$contract) { header('Location: contracts.php'); exit; }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_csrf();
        $client_id = intval($_POST['client_id'] ?? 0);
        $client = db()->fetchOne("SELECT id, name, email FROM users WHERE id = ? AND role = 'client'", [$client_id]);
        if (!$client) { $error = 'Selecione um cliente válido'; }
        else {
            $service_slug = sanitize($_POST['service_slug'] ?? '');
            $service = db()->fetchOne("SELECT slug, name FROM services_db WHERE slug = ? AND is_active = 1", [$service_slug]);
            $service_name = $service ? $service['name'] : sanitize($_POST['service_name'] ?? $service_slug);
            $start_date = sanitize($_POST['start_date'] ?? date('Y-m-d'));
            $end_date = !empty($_POST['end_date']) ? sanitize($_POST['end_date']) : null;
            $value = floatval($_POST['value'] ?? 0);
            $payment_frequency = sanitize($_POST['payment_frequency'] ?? 'monthly');
            $status = sanitize($_POST['status'] ?? 'active');
            $description = sanitize($_POST['description'] ?? '');

            $data = [
                'client_id' => $client_id,
                'service_slug' => $service_slug,
                'service_name' => $service_name,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'value' => $value,
                'payment_frequency' => $payment_frequency,
                'status' => $status,
                'description' => $description,
            ];

            if ($value <= 0) { $error = 'O valor deve ser maior que zero'; }
            else {
                if ($isEdit) {
                    if (!empty($_FILES['contract_file']['name']) && $_FILES['contract_file']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = __DIR__ . '/../uploads/contracts';
                        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
                        $ext = pathinfo($_FILES['contract_file']['name'], PATHINFO_EXTENSION);
                        $filename = 'contract_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                        $dest = $uploadDir . '/' . $filename;
                        if (move_uploaded_file($_FILES['contract_file']['tmp_name'], $dest)) {
                            if ($contract['file_path'] && file_exists($uploadDir . '/' . $contract['file_path'])) {
                                @unlink($uploadDir . '/' . $contract['file_path']);
                            }
                            $data['file_path'] = $filename;
                        }
                    }
                    db()->update('contracts', $data, 'id = :id', ['id' => $id]);
                    logActivity($user['id'], 'update_contract', "Contrato #{$id} actualizado");
                    header('Location: contracts.php?msg=updated');
                    exit;
                } else {
                    $data['contract_number'] = generateContractNo();
                    $data['created_by'] = $user['id'];

                    if (!empty($_FILES['contract_file']['name']) && $_FILES['contract_file']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = __DIR__ . '/../uploads/contracts';
                        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
                        $ext = pathinfo($_FILES['contract_file']['name'], PATHINFO_EXTENSION);
                        $filename = 'contract_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                        $dest = $uploadDir . '/' . $filename;
                        if (move_uploaded_file($_FILES['contract_file']['tmp_name'], $dest)) {
                            $data['file_path'] = $filename;
                        }
                    }

                    $newId = db()->insert('contracts', $data);
                    logActivity($user['id'], 'create_contract', "Contrato {$data['contract_number']} criado");

                    if ($data['end_date'] && $data['status'] === 'active') {
                        $daysLeft = (strtotime($data['end_date']) - time()) / 86400;
                        if ($daysLeft <= 30 && $daysLeft > 0 && $service) {
                            try {
                                $html = emailTemplateContractExpiring($client['name'], $data['contract_number'], ceil($daysLeft));
                                sendEmail($client['email'], 'Contrato a Expirar - ' . $data['contract_number'], $html);
                            } catch (Exception $e) {
                                error_log("Email contract notification failed: " . $e->getMessage());
                            }
                        }
                    }

                    header('Location: contracts.php?msg=created');
                    exit;
                }
            }
        }
    }

    $clients = db()->fetchAll("SELECT id, name, email FROM users WHERE role = 'client' ORDER BY name ASC");
    $services = db()->fetchAll("SELECT slug, name FROM services_db WHERE is_active = 1 ORDER BY name ASC");
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $isEdit ? 'Editar' : 'Novo' ?> Contrato - ANGONUEVE CRM</title>
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
                <div class="header-search"><i class="fas fa-file-signature"></i> <span><?= $isEdit ? 'Editar' : 'Novo' ?> Contrato</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <div class="detail-card">
                    <form method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <h3 style="margin-bottom:20px;font-size:1rem;color:var(--primary);"><i class="fas fa-user"></i> Cliente</h3>
                        <div class="form-group">
                            <label>Cliente *</label>
                            <select name="client_id" id="clientSelect" required>
                                <option value="">Selecionar cliente...</option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($isEdit && $contract['client_id'] == $c['id']) ? 'selected' : '' ?>>
                                        <?= sanitize($c['name']) ?> (<?= sanitize($c['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <h3 style="margin:24px 0 20px;font-size:1rem;color:var(--primary);"><i class="fas fa-cogs"></i> Serviço</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Serviço *</label>
                                <select name="service_slug" id="serviceSelect" required onchange="fillServiceName()">
                                    <option value="">Selecionar serviço...</option>
                                    <?php foreach ($services as $s): ?>
                                        <option value="<?= sanitize($s['slug']) ?>" data-name="<?= sanitize($s['name']) ?>" <?= ($isEdit && $contract['service_slug'] === $s['slug']) ? 'selected' : '' ?>>
                                            <?= sanitize($s['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nome do Serviço</label>
                                <input type="text" name="service_name" id="serviceName" value="<?= $isEdit ? sanitize($contract['service_name']) : '' ?>" readonly>
                            </div>
                        </div>

                        <h3 style="margin:24px 0 20px;font-size:1rem;color:var(--primary);"><i class="fas fa-calendar"></i> Período</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Data de Início *</label>
                                <input type="date" name="start_date" value="<?= $isEdit ? $contract['start_date'] : date('Y-m-d') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Data de Fim (opcional)</label>
                                <input type="date" name="end_date" value="<?= $isEdit && $contract['end_date'] ? $contract['end_date'] : '' ?>">
                            </div>
                        </div>

                        <h3 style="margin:24px 0 20px;font-size:1rem;color:var(--primary);"><i class="fas fa-calculator"></i> Valores</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Valor (Kz) *</label>
                                <input type="number" name="value" value="<?= $isEdit ? $contract['value'] : '' ?>" step="0.01" min="0" required>
                            </div>
                            <div class="form-group">
                                <label>Frequência de Pagamento</label>
                                <select name="payment_frequency">
                                    <option value="monthly" <?= $isEdit && $contract['payment_frequency'] === 'monthly' ? 'selected' : '' ?>>Mensal</option>
                                    <option value="yearly" <?= $isEdit && $contract['payment_frequency'] === 'yearly' ? 'selected' : '' ?>>Anual</option>
                                    <option value="onetime" <?= $isEdit && $contract['payment_frequency'] === 'onetime' ? 'selected' : '' ?>>Único</option>
                                </select>
                            </div>
                        </div>

                        <h3 style="margin:24px 0 20px;font-size:1rem;color:var(--primary);"><i class="fas fa-info-circle"></i> Detalhes</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="active" <?= $isEdit && $contract['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                                    <option value="expired" <?= $isEdit && $contract['status'] === 'expired' ? 'selected' : '' ?>>Expirado</option>
                                    <option value="cancelled" <?= $isEdit && $contract['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ficheiro do Contrato (PDF)</label>
                                <input type="file" name="contract_file" accept=".pdf,application/pdf">
                                <?php if ($isEdit && $contract['file_path']): ?>
                                    <small style="display:block;margin-top:4px;color:var(--text-muted);">Ficheiro actual: <?= sanitize($contract['file_path']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea name="description"><?= $isEdit ? sanitize($contract['description'] ?? '') : '' ?></textarea>
                        </div>

                        <div style="display:flex;gap:12px;margin-top:24px;padding-top:24px;border-top:1px solid var(--border);">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar' : 'Criar' ?> Contrato</button>
                            <a href="contracts.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
    function fillServiceName() {
        const sel = document.getElementById('serviceSelect');
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('serviceName').value = opt.dataset.name || '';
        } else {
            document.getElementById('serviceName').value = '';
        }
    }
    <?php if ($isEdit): ?>
    fillServiceName();
    <?php endif; ?>
    </script>
    </body>
    </html>
    <?php
    exit;
}

if ($action === 'view' && $id) {
    $contract = db()->fetchOne("SELECT c.*, u.name as client_name, u.email as client_email FROM contracts c LEFT JOIN users u ON c.client_id = u.id WHERE c.id = ?", [$id]);
    if (!$contract) { header('Location: contracts.php'); exit; }
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contrato <?= sanitize($contract['contract_number']) ?> - ANGONUEVE CRM</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/admin.css">
    </head>
    <body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-file-signature"></i> <span>Contrato <?= sanitize($contract['contract_number']) ?></span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <div class="detail-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                        <h2 style="margin:0;font-size:1.3rem;color:var(--text);"><?= sanitize($contract['contract_number']) ?></h2>
                        <div><?= statusBadge($contract['status']) ?></div>
                    </div>
                    <table class="detail-table">
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;width:180px;">Cliente</td><td style="padding:8px 0;"><strong><?= sanitize($contract['client_name']) ?></strong></td></tr>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;">Email</td><td style="padding:8px 0;"><?= sanitize($contract['client_email']) ?></td></tr>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;">Serviço</td><td style="padding:8px 0;"><?= sanitize($contract['service_name']) ?></td></tr>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;">Valor</td><td style="padding:8px 0;"><strong style="color:var(--primary);">Kz <?= number_format($contract['value'], 2, ',', ' ') ?></strong></td></tr>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;">Frequência</td><td style="padding:8px 0;"><?= $contract['payment_frequency'] === 'monthly' ? 'Mensal' : ($contract['payment_frequency'] === 'yearly' ? 'Anual' : 'Único') ?></td></tr>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;">Data de Início</td><td style="padding:8px 0;"><?= formatDate($contract['start_date'], 'd/m/Y') ?></td></tr>
                        <?php if ($contract['end_date']): ?>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;">Data de Fim</td><td style="padding:8px 0;"><?= formatDate($contract['end_date'], 'd/m/Y') ?></td></tr>
                        <?php endif; ?>
                        <?php if ($contract['description']): ?>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;vertical-align:top;">Descrição</td><td style="padding:8px 0;"><?= nl2br(sanitize($contract['description'])) ?></td></tr>
                        <?php endif; ?>
                        <?php if ($contract['file_path']): ?>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;">Ficheiro</td><td style="padding:8px 0;"><a href="../uploads/contracts/<?= urlencode($contract['file_path']) ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-file-pdf"></i> Ver Contrato</a></td></tr>
                        <?php endif; ?>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;">Criado em</td><td style="padding:8px 0;"><?= formatDate($contract['created_at'], 'd/m/Y H:i') ?></td></tr>
                        <tr><td style="color:var(--text-muted);padding:8px 16px 8px 0;">Actualizado em</td><td style="padding:8px 0;"><?= formatDate($contract['updated_at'], 'd/m/Y H:i') ?></td></tr>
                    </table>
                    <div style="display:flex;gap:12px;margin-top:24px;padding-top:24px;border-top:1px solid var(--border);">
                        <a href="?action=edit&id=<?= $contract['id'] ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
                        <a href="contracts.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
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
        $contract = db()->fetchOne("SELECT contract_number, file_path FROM contracts WHERE id = ?", [$id]);
        if ($contract && $contract['file_path']) {
            $filePath = __DIR__ . '/../uploads/contracts/' . $contract['file_path'];
            if (file_exists($filePath)) { @unlink($filePath); }
        }
        db()->delete('contracts', 'id = ?', [$id]);
        logActivity($user['id'], 'delete_contract', "Contrato {$contract['contract_number']} eliminado");
        header('Location: contracts.php?msg=deleted');
        exit;
    } else {
        die('Apenas administradores podem eliminar');
    }
}

$page = max(1, intval($_GET['page'] ?? 1));
$status = sanitize($_GET['status'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;
$where = [];
$params = [];
if ($status) { $where[] = 'c.status = ?'; $params[] = $status; }
if ($search) { $where[] = '(u.name LIKE ? OR c.contract_number LIKE ? OR c.service_name LIKE ?)'; $params[] = "%{$search}%"; $params[] = "%{$search}%"; $params[] = "%{$search}%"; }
$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$total = db()->fetchOne("SELECT COUNT(*) as total FROM contracts c LEFT JOIN users u ON c.client_id = u.id {$whereClause}", $params)['total'];
$contracts = db()->fetchAll("SELECT c.*, u.name as client_name FROM contracts c LEFT JOIN users u ON c.client_id = u.id {$whereClause} ORDER BY c.created_at DESC LIMIT ? OFFSET ?", array_merge($params, [$perPage, $offset]));
$msg = sanitize($_GET['msg'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contratos - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-file-signature"></i> <span>Contratos</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content">
            <?php if ($msg === 'created'): ?><div class="alert alert-success">Contrato criado com sucesso!</div><?php endif; ?>
            <?php if ($msg === 'updated'): ?><div class="alert alert-success">Contrato actualizado com sucesso!</div><?php endif; ?>
            <?php if ($msg === 'deleted'): ?><div class="alert alert-success">Contrato eliminado!</div><?php endif; ?>

            <div class="table-controls">
                <div class="filter-tabs">
                    <a href="?" class="btn-sm <?= !$status ? 'active' : '' ?>">Todos</a>
                    <a href="?status=active" class="btn-sm <?= $status === 'active' ? 'active' : '' ?>">Activos</a>
                    <a href="?status=expired" class="btn-sm <?= $status === 'expired' ? 'active' : '' ?>">Expirados</a>
                    <a href="?status=cancelled" class="btn-sm <?= $status === 'cancelled' ? 'active' : '' ?>">Cancelados</a>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <form method="GET" style="display:flex;gap:8px;">
                        <input type="text" name="search" placeholder="Pesquisar..." value="<?= sanitize($search) ?>" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;font-size:0.85rem;outline:none;">
                        <button type="submit" class="btn-sm" style="background:var(--primary);color:#fff;border:none;"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="?action=create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Contrato</a>
                </div>
            </div>
            <div class="table-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Contrato</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Valor</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($contracts)): ?>
                            <tr><td colspan="8" class="empty-state">Nenhum contrato encontrado</td></tr>
                        <?php else: ?>
                            <?php foreach ($contracts as $c): ?>
                                <tr>
                                    <td><strong><?= sanitize($c['contract_number']) ?></strong></td>
                                    <td><?= sanitize($c['client_name']) ?></td>
                                    <td><?= sanitize($c['service_name'] ?? '-') ?></td>
                                    <td>Kz <?= number_format($c['value'], 2, ',', ' ') ?></td>
                                    <td><?= $c['start_date'] ? formatDate($c['start_date'], 'd/m/Y') : '-' ?></td>
                                    <td><?= $c['end_date'] ? formatDate($c['end_date'], 'd/m/Y') : '-' ?></td>
                                    <td><?= statusBadge($c['status']) ?></td>
                                    <td class="actions">
                                        <a href="?action=view&id=<?= $c['id'] ?>" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="?action=edit&id=<?= $c['id'] ?>" class="btn-icon" title="Editar"><i class="fas fa-edit"></i></a>
                                        <a href="?action=delete&id=<?= $c['id'] ?>" class="btn-icon danger" title="Eliminar" onclick="return confirm('Eliminar contrato <?= sanitize($c['contract_number']) ?>?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if ($total > $perPage): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                            <a href="?page=<?= $i ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
