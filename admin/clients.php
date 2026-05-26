<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
requirePermission('clients');

$user = currentUser();
$action = $_GET['action'] ?? '';
$clientId = intval($_GET['id'] ?? 0);

if ($action === 'view' && $clientId) {
    $client = db()->fetchOne("SELECT * FROM users WHERE id = ? AND role = 'client'", [$clientId]);
    if (!$client) { header('Location: clients.php'); exit; }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_csrf();
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone'] ?? '');
        $nif = sanitize($_POST['nif'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $status = sanitize($_POST['status']);

        if ($nif && !validateNIF($nif)) {
            $error = 'NIF inválido. O NIF angolano tem 10 dígitos (ex: 1234567-890).';
        } else {
            db()->update('users', [
                'name' => $name, 'email' => $email, 'phone' => $phone,
                'nif' => $nif, 'address' => $address, 'status' => $status
            ], 'id = :id', ['id' => $clientId]);
            logActivity($user['id'], 'update', "Cliente #{$clientId} editado");
            $success = 'Cliente actualizado com sucesso!';
            $client = db()->fetchOne("SELECT * FROM users WHERE id = ?", [$clientId]);
        }
    }

    $clientInvoices = getClientInvoices($clientId);
    $clientPayments = getClientPayments($clientId);
    $clientServices = getClientServices($clientId);
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cliente #<?= $clientId ?> - ANGONUEVE CRM</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/admin.css">
        <style>
            .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
            .form-group { margin-bottom: 16px; }
            .form-group label { display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; }
            .form-group input, .form-group select, .form-group textarea {
                width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px;
                background: rgba(255,255,255,0.03); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.9rem; outline: none;
            }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--primary); }
            .form-group textarea { min-height: 60px; resize: vertical; }
            select option { background: #0d1f3c; }
            .nif-valid { color: var(--success); font-size: 0.8rem; }
            .nif-invalid { color: var(--danger); font-size: 0.8rem; }
        </style>
    </head>
    <body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-user"></i> <span><?= sanitize($client['name']) ?></span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <?php if (isset($success)): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                <?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

                <div class="detail-card" style="margin-bottom:20px;">
                    <form method="POST">
                        <?= csrf_field() ?>
                        <h3 style="margin-bottom:16px;font-size:1rem;color:var(--primary);"><i class="fas fa-edit"></i> Editar Cliente</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nome</label>
                                <input type="text" name="name" value="<?= sanitize($client['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="<?= sanitize($client['email']) ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Telefone</label>
                                <input type="text" name="phone" value="<?= sanitize($client['phone'] ?? '') ?>" placeholder="+244 900 000 000">
                            </div>
                            <div class="form-group">
                                <label>NIF <span id="nifStatus"></span></label>
                                <input type="text" name="nif" id="nifInput" value="<?= sanitize($client['nif'] ?? '') ?>" placeholder="1234567-890" maxlength="11" oninput="checkNIF(this.value)">
                                <small style="color:var(--text-muted);font-size:0.75rem;">NIF angolano com 10 dígitos. Formato: XXXXXXX-XXX</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Endereço</label>
                            <textarea name="address"><?= sanitize($client['address'] ?? '') ?></textarea>
                        </div>
                        <div class="form-row" style="align-items:end;">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="active" <?= $client['status'] === 'active' ? 'selected' : '' ?>>Activo</option>
                                    <option value="pending" <?= $client['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="blocked" <?= $client['status'] === 'blocked' ? 'selected' : '' ?>>Bloqueado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Registado em</label>
                                <input type="text" value="<?= formatDate($client['created_at'], 'd/m/Y H:i') ?>" disabled>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top:8px;"><i class="fas fa-save"></i> Guardar Alterações</button>
                        <a href="clients.php" class="btn btn-secondary" style="margin-top:8px;"><i class="fas fa-arrow-left"></i> Voltar</a>
                    </form>
                </div>

                <div class="stats-grid" style="margin-bottom:20px;">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fas fa-file-invoice"></i></div>
                        <div class="stat-info">
                            <h3><?= count($clientInvoices) ?></h3>
                            <p>Facturas</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-credit-card"></i></div>
                        <div class="stat-info">
                            <h3><?= count($clientPayments) ?></h3>
                            <p>Pagamentos</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="fas fa-cogs"></i></div>
                        <div class="stat-info">
                            <h3><?= count($clientServices) ?></h3>
                            <p>Serviços Activos</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="fas fa-shopping-cart"></i></div>
                        <div class="stat-info">
                            <h3><?= db()->count('orders', "customer_email = ?", [$client['email']]) ?></h3>
                            <p>Encomendas</p>
                        </div>
                    </div>
                </div>

                <?php if (!empty($clientInvoices)): ?>
                <div class="table-card" style="margin-bottom:16px;">
                    <div style="padding:12px 20px;border-bottom:1px solid var(--border);font-weight:600;">Facturas</div>
                    <table class="admin-table">
                        <thead><tr><th>Factura</th><th>Valor</th><th>Status</th><th>Data</th></tr></thead>
                        <tbody>
                            <?php foreach ($clientInvoices as $inv): ?>
                            <tr>
                                <td><?= sanitize($inv['invoice_no']) ?></td>
                                <td>Kz <?= number_format($inv['total'], 0, ',', ' ')?></td>
                                <td><?= statusBadge($inv['status']) ?></td>
                                <td><?= formatDate($inv['created_at'], 'd/m/Y') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script>
    function checkNIF(val) {
        const s = document.getElementById('nifStatus');
        const digits = val.replace(/\D/g, '');
        if (digits.length === 10) {
            fetch('../api/validate-nif.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'nif=' + encodeURIComponent(digits)
            })
            .then(r => r.json())
            .then(d => {
                s.className = d.valid ? 'nif-valid' : 'nif-invalid';
                s.innerHTML = d.valid ? '<i class="fas fa-check-circle"></i> NIF válido' : '<i class="fas fa-times-circle"></i> NIF inválido';
            });
        } else {
            s.className = '';
            s.innerHTML = '';
        }
    }
    </script>
    </body>
    </html>
    <?php
    exit;
}

if ($action === 'approve' && $clientId) {
    db()->update('users', ['status' => 'active'], 'id = ? AND role = ?', [$clientId, 'client']);
    logActivity($user['id'], 'approve', "Cliente #{$clientId} aprovado");
    try {
        $client = db()->fetchOne("SELECT name, email FROM users WHERE id = ?", [$clientId]);
        if ($client) {
            $siteName = getSetting('site_name', 'ANGONUEVE');
            $subject = 'Conta Aprovada - ' . $siteName;
            $body = '
            <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f7fa;">
                <div style="background: #1a8a3c; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                    <h1 style="color: #ffffff; margin: 0; font-size: 22px;">Conta Aprovada</h1>
                </div>
                <div style="background: #ffffff; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <p style="color: #333; font-size: 16px;">Olá <strong>' . $client['name'] . '</strong>,</p>
                    <p style="color: #555; font-size: 15px;">A sua conta no ' . $siteName . ' foi aprovada com sucesso!</p>
                    <p style="color: #555; font-size: 15px;">Já pode aceder à sua área de cliente e consultar os seus serviços.</p>
                    <div style="text-align: center; margin: 25px 0;">
                        <a href="' . SITE_URL . '/client/login.php" style="background: #1a8a3c; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-size: 15px; display: inline-block;">Aceder à Minha Conta</a>
                    </div>
                    <p style="color: #999; font-size: 13px; text-align: center;">' . $siteName . '</p>
                </div>
            </div>';
            sendEmail($client['email'], $subject, $body);
        }
    } catch (Exception $e) {
        error_log("Email client approval notification failed: " . $e->getMessage());
    }
    header('Location: clients.php?msg=approved');
    exit;
}

if ($action === 'block' && $clientId) {
    db()->update('users', ['status' => 'blocked'], 'id = ? AND role = ?', [$clientId, 'client']);
    logActivity($user['id'], 'block', "Cliente #{$clientId} bloqueado");
    header('Location: clients.php?msg=blocked');
    exit;
}

$statusFilter = sanitize($_GET['status'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$where = "role = 'client'";
$params = [];
if ($statusFilter === 'pending') { $where .= " AND status = 'pending'"; }
elseif ($statusFilter === 'active') { $where .= " AND status = 'active'"; }
elseif ($statusFilter === 'blocked') { $where .= " AND status = 'blocked'"; }
if ($search) { $where .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR nif LIKE ?)"; $params = array_fill(0, 4, "%{$search}%"); }

$clients = db()->fetchAll("SELECT * FROM users WHERE {$where} ORDER BY created_at DESC", $params);
$pendingCount = db()->count('users', "role = 'client' AND status = 'pending'");

$msg = $_GET['msg'] ?? '';
$msgText = '';
if ($msg === 'approved') $msgText = 'Cliente aprovado com sucesso!';
if ($msg === 'blocked') $msgText = 'Cliente bloqueado com sucesso!';

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - ANGONUEVE CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .search-bar { display: flex; gap: 8px; align-items: center; }
        .search-bar input { padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; background: rgba(255,255,255,0.03); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.85rem; outline: none; min-width: 250px; }
        .search-bar input:focus { border-color: var(--primary); }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-users"></i> <span>Gestão de Clientes</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <?php if ($msgText): ?><div class="alert alert-success"><?= $msgText ?></div><?php endif; ?>
                <div class="table-controls">
                    <div class="filter-tabs">
                        <a href="clients.php" class="btn btn-sm <?= !$statusFilter && !$search ? 'btn-primary' : 'btn-secondary' ?>">Todos</a>
                        <a href="clients.php?status=pending" class="btn btn-sm <?= $statusFilter === 'pending' ? 'btn-primary' : 'btn-secondary' ?>">
                            Pendentes <?php if ($pendingCount > 0): ?><span class="badge badge-danger" style="margin-left:4px;"><?= $pendingCount ?></span><?php endif; ?>
                        </a>
                        <a href="clients.php?status=active" class="btn btn-sm <?= $statusFilter === 'active' ? 'btn-primary' : 'btn-secondary' ?>">Activos</a>
                        <a href="clients.php?status=blocked" class="btn btn-sm <?= $statusFilter === 'blocked' ? 'btn-primary' : 'btn-secondary' ?>">Bloqueados</a>
                    </div>
                    <form method="GET" class="search-bar">
                        <input type="text" name="search" placeholder="Pesquisar por nome, email, NIF..." value="<?= sanitize($search) ?>">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                        <?php if ($search): ?><a href="clients.php" class="btn btn-sm btn-secondary"><i class="fas fa-times"></i></a><?php endif; ?>
                    </form>
                </div>
                <div class="table-card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email / NIF</th>
                                <th>Telefone</th>
                                <th>Registo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clients)): ?>
                                <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">Nenhum cliente encontrado</td></tr>
                            <?php else: ?>
                                <?php foreach ($clients as $c): ?>
                                    <tr class="<?= $c['status'] === 'pending' ? 'row-unread' : '' ?>">
                                        <td><strong><?= sanitize($c['name']) ?></strong></td>
                                        <td>
                                            <?= sanitize($c['email']) ?>
                                            <?php if ($c['nif']): ?><br><code style="font-size:0.75rem;">NIF: <?= sanitize(formatNIF($c['nif'])) ?></code><?php endif; ?>
                                        </td>
                                        <td><?= sanitize($c['phone'] ?: '—') ?></td>
                                        <td><?= formatDate($c['created_at'], 'd/m/Y') ?></td>
                                        <td>
                                            <?php if ($c['status'] === 'pending'): ?><span class="badge badge-warning">Pendente</span>
                                            <?php elseif ($c['status'] === 'active'): ?><span class="badge badge-success">Activo</span>
                                            <?php elseif ($c['status'] === 'blocked'): ?><span class="badge badge-danger">Bloqueado</span><?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a href="?action=view&id=<?= $c['id'] ?>" class="btn-icon" title="Ver/Editar"><i class="fas fa-eye"></i></a>
                                                <?php if ($c['status'] === 'pending'): ?>
                                                    <a href="clients.php?action=approve&id=<?= $c['id'] ?>" class="btn-icon" style="color:var(--success);" title="Aprovar" onclick="return confirm('Aprovar este cliente?')"><i class="fas fa-check"></i></a>
                                                <?php endif; ?>
                                                <?php if ($c['status'] === 'active'): ?>
                                                    <a href="clients.php?action=block&id=<?= $c['id'] ?>" class="btn-icon danger" title="Bloquear" onclick="return confirm('Bloquear este cliente?')"><i class="fas fa-ban"></i></a>
                                                <?php endif; ?>
                                                <?php if ($c['status'] === 'blocked'): ?>
                                                    <a href="clients.php?action=approve&id=<?= $c['id'] ?>" class="btn-icon" style="color:var(--success);" title="Reativar"><i class="fas fa-check"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
