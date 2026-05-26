<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('payslips');

$user = currentUser();
$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);

if ($action === 'view' && $id) {
    $payslip = db()->fetchOne("SELECT * FROM payslips WHERE id = ?", [$id]);
    if (!$payslip) { header('Location: payslips.php'); exit; }
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recibo #<?= $id ?> - ANGONUEVE CRM</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/admin.css">
    </head>
    <body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-file-invoice-dollar"></i> <span>Recibo #<?= $id ?></span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <div class="detail-card">
                    <div class="detail-header">
                        <h2>Recibo de Vencimento #<?= $id ?></h2>
                        <?= statusBadge($payslip['status']) ?>
                    </div>
                    <div class="detail-meta" style="grid-template-columns:repeat(3,1fr);">
                        <p><i class="fas fa-user"></i> <?= sanitize($payslip['employee_name']) ?></p>
                        <p><i class="fas fa-briefcase"></i> <?= sanitize($payslip['position']) ?></p>
                        <p><i class="fas fa-calendar-alt"></i> Período: <?= sanitize($payslip['month_year']) ?></p>
                        <p><i class="fas fa-money-bill"></i> Salário: Kz <?= number_format($payslip['salary'], 0, ',', ' ') ?></p>
                        <p><i class="fas fa-plus-circle" style="color:var(--success);"></i> Bónus: Kz <?= number_format($payslip['bonus'], 0, ',', ' ') ?></p>
                        <p><i class="fas fa-minus-circle" style="color:var(--danger);"></i> Deduções: Kz <?= number_format($payslip['deductions'], 0, ',', ' ') ?></p>
                        <p><i class="fas fa-calculator"></i> <strong>Líquido: Kz <?= number_format($payslip['net_salary'], 0, ',', ' ') ?></strong></p>
                        <p><i class="fas fa-clock"></i> Gerado: <?= formatDate($payslip['generated_at'] ?: $payslip['created_at']) ?></p>
                        <?php if ($payslip['paid_at']): ?>
                            <p><i class="fas fa-check-circle" style="color:var(--success);"></i> Pago em: <?= formatDate($payslip['paid_at']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if ($payslip['notes']): ?>
                        <div style="margin-top:16px;padding:16px;background:rgba(255,255,255,0.03);border-radius:8px;">
                            <h4 style="margin-bottom:8px;color:var(--text-muted);font-size:0.85rem;">Notas</h4>
                            <p style="color:var(--text);"><?= nl2br(sanitize($payslip['notes'])) ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="detail-actions">
                        <a href="payslips.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                        <a href="payslip-pdf.php?id=<?= $payslip['id'] ?>" class="btn btn-primary" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>
                        <?php if ($payslip['status'] === 'pending'): ?>
                            <a href="?action=mark-paid&id=<?= $payslip['id'] ?>" class="btn btn-success" onclick="return confirm('Marcar este recibo como pago?')"><i class="fas fa-check"></i> Marcar Pago</a>
                        <?php endif; ?>
                        <?php if (isAdmin()): ?>
                            <a href="?action=delete&id=<?= $payslip['id'] ?>" class="btn btn-danger" onclick="return confirm('Eliminar este recibo?')"><i class="fas fa-trash"></i> Eliminar</a>
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

if ($action === 'generate-pdf' && $id) {
    header('Location: payslip-pdf.php?id=' . $id);
    exit;
}

if ($action === 'mark-paid' && $id) {
    $ps = db()->fetchOne("SELECT id, employee_name, month_year, status FROM payslips WHERE id = ?", [$id]);
    if ($ps && $ps['status'] !== 'paid') {
        db()->update('payslips', ['status' => 'paid', 'paid_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $id]);
        logActivity($user['id'], 'mark_paid_payslip', "Recibo marcado pago - {$ps['employee_name']} ({$ps['month_year']})");
    }
    header('Location: payslips.php?msg=paid');
    exit;
}

if ($action === 'delete' && $id) {
    if (isAdmin()) {
        $ps = db()->fetchOne("SELECT employee_name, month_year FROM payslips WHERE id = ?", [$id]);
        db()->delete('payslips', 'id = ?', [$id]);
        logActivity($user['id'], 'delete_payslip', "Recibo eliminado - {$ps['employee_name']} ({$ps['month_year']})");
        header('Location: payslips.php?msg=deleted');
        exit;
    } else {
        die('Apenas administradores podem eliminar');
    }
}

if ($action === 'create' || $action === 'edit') {
    $isEdit = $action === 'edit' && $id;
    $payslip = null;
    if ($isEdit) {
        $payslip = db()->fetchOne("SELECT * FROM payslips WHERE id = ?", [$id]);
        if (!$payslip) { header('Location: payslips.php'); exit; }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_csrf();
        $data = [
            'employee_id' => intval($_POST['employee_id'] ?? 0),
            'employee_name' => sanitize($_POST['employee_name']),
            'position' => sanitize($_POST['position']),
            'salary' => floatval($_POST['salary'] ?? 0),
            'month_year' => sanitize($_POST['month_year']),
            'bonus' => floatval($_POST['bonus'] ?? 0),
            'deductions' => floatval($_POST['deductions'] ?? 0),
            'notes' => sanitize($_POST['notes']),
        ];
        $data['net_salary'] = $data['salary'] + $data['bonus'] - $data['deductions'];

        if (empty($data['employee_name']) || empty($data['month_year']) || $data['salary'] <= 0) {
            $error = 'Preencha todos os campos obrigatórios (funcionário, mês/ano, salário)';
        } elseif ($data['employee_id'] <= 0) {
            $error = 'Selecione um funcionário válido';
        } else {
            if ($isEdit) {
                db()->update('payslips', $data, 'id = :id', ['id' => $id]);
                logActivity($user['id'], 'update_payslip', "Recibo #{$id} actualizado - {$data['employee_name']} ({$data['month_year']})");
                header('Location: payslips.php?msg=updated');
                exit;
            } else {
                $data['created_by'] = $user['id'];
                $data['generated_at'] = date('Y-m-d H:i:s');
                db()->insert('payslips', $data);
                logActivity($user['id'], 'create_payslip', "Recibo criado - {$data['employee_name']} ({$data['month_year']})");
                header('Location: payslips.php?msg=created');
                exit;
            }
        }
    }

    $employees = db()->fetchAll("SELECT id, name, position, salary FROM users WHERE role = 'employee' AND status = 'active' ORDER BY name ASC");
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $isEdit ? 'Editar' : 'Novo' ?> Recibo de Vencimento - ANGONUEVE CRM</title>
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
                <div class="header-search"><i class="fas fa-file-invoice-dollar"></i> <span><?= $isEdit ? 'Editar' : 'Novo' ?> Recibo de Vencimento</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <div class="detail-card">
                    <form method="POST">
                        <?= csrf_field() ?>
                        <h3 style="margin-bottom:20px;font-size:1rem;color:var(--primary);"><i class="fas fa-user-tie"></i> Dados do Funcionário</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Funcionário *</label>
                                <select name="employee_id" id="employeeSelect" required onchange="fillEmployeeData()">
                                    <option value="">Selecionar funcionário...</option>
                                    <?php foreach ($employees as $e): ?>
                                        <option value="<?= $e['id'] ?>"
                                            data-name="<?= sanitize($e['name']) ?>"
                                            data-position="<?= sanitize($e['position']) ?>"
                                            data-salary="<?= $e['salary'] ?>"
                                            <?= ($isEdit && $payslip['employee_id'] == $e['id']) ? 'selected' : '' ?>>
                                            <?= sanitize($e['name']) ?> — <?= sanitize($e['position']) ?> (Kz <?= number_format($e['salary'], 0, ',', ' ') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Mês/Ano *</label>
                                <input type="text" name="month_year" id="monthYear" value="<?= $isEdit ? sanitize($payslip['month_year']) : date('Y-m') ?>" placeholder="YYYY-MM" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nome *</label>
                                <input type="text" name="employee_name" id="employeeName" value="<?= $isEdit ? sanitize($payslip['employee_name']) : '' ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Cargo</label>
                                <input type="text" name="position" id="employeePosition" value="<?= $isEdit ? sanitize($payslip['position']) : '' ?>">
                            </div>
                        </div>

                        <h3 style="margin:24px 0 20px;font-size:1rem;color:var(--primary);"><i class="fas fa-calculator"></i> Valores</h3>
                        <div class="form-row-3">
                            <div class="form-group">
                                <label>Salário (Kz) *</label>
                                <input type="number" name="salary" id="salary" value="<?= $isEdit ? $payslip['salary'] : '' ?>" step="0.01" min="0" required onchange="calcNet()">
                            </div>
                            <div class="form-group">
                                <label>Bónus (Kz)</label>
                                <input type="number" name="bonus" id="bonus" value="<?= $isEdit ? $payslip['bonus'] : '0' ?>" step="0.01" min="0" onchange="calcNet()">
                            </div>
                            <div class="form-group">
                                <label>Deduções (Kz)</label>
                                <input type="number" name="deductions" id="deductions" value="<?= $isEdit ? $payslip['deductions'] : '0' ?>" step="0.01" min="0" onchange="calcNet()">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Valor Líquido: <strong id="netDisplay" style="color:var(--primary);font-size:1.2rem;">Kz 0,00</strong></label>
                            <input type="hidden" name="net_salary" id="netInput" value="<?= $isEdit ? $payslip['net_salary'] : '0' ?>">
                        </div>
                        <div class="form-group">
                            <label>Notas</label>
                            <textarea name="notes"><?= $isEdit ? sanitize($payslip['notes'] ?? '') : '' ?></textarea>
                        </div>
                        <div style="display:flex;gap:12px;margin-top:24px;padding-top:24px;border-top:1px solid var(--border);">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $isEdit ? 'Actualizar' : 'Criar' ?> Recibo</button>
                            <a href="payslips.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script>
    function fillEmployeeData() {
        const sel = document.getElementById('employeeSelect');
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.value) {
            document.getElementById('employeeName').value = opt.dataset.name || '';
            document.getElementById('employeePosition').value = opt.dataset.position || '';
            document.getElementById('salary').value = opt.dataset.salary || '0';
            calcNet();
        }
    }
    function calcNet() {
        const salary = parseFloat(document.getElementById('salary').value) || 0;
        const bonus = parseFloat(document.getElementById('bonus').value) || 0;
        const deductions = parseFloat(document.getElementById('deductions').value) || 0;
        const net = salary + bonus - deductions;
        document.getElementById('netDisplay').textContent = 'Kz ' + net.toLocaleString('pt-PT', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('netInput').value = net.toFixed(2);
    }
    <?php if (!$isEdit): ?>calcNet();<?php endif; ?>
    </script>
    </body>
    </html>
    <?php
    exit;
}

$page = max(1, intval($_GET['page'] ?? 1));
$status = sanitize($_GET['status'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;
$where = [];
$params = [];
if ($status) { $where[] = 'status = ?'; $params[] = $status; }
if ($search) { $where[] = '(employee_name LIKE ? OR month_year LIKE ?)'; $s = "%$search%"; $params[] = $s; $params[] = $s; }
$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$total = current(db()->query("SELECT COUNT(*) as total FROM payslips {$whereClause}", $params)->fetch());
$payslips = db()->fetchAll("SELECT * FROM payslips {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?", array_merge($params, [$perPage, $offset]));
$msg = sanitize($_GET['msg'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibos de Vencimento - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-file-invoice-dollar"></i> <span>Recibos de Vencimento</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content">
            <?php if ($msg === 'created'): ?><div class="alert alert-success">Recibo criado com sucesso!</div><?php endif; ?>
            <?php if ($msg === 'updated'): ?><div class="alert alert-success">Recibo actualizado com sucesso!</div><?php endif; ?>
            <?php if ($msg === 'deleted'): ?><div class="alert alert-success">Recibo eliminado!</div><?php endif; ?>
            <?php if ($msg === 'paid'): ?><div class="alert alert-success">Recibo marcado como pago!</div><?php endif; ?>

            <div class="table-controls">
                <div class="filter-tabs">
                    <a href="?search=<?= $search ?>" class="btn-sm <?= !$status ? 'active' : '' ?>">Todos</a>
                    <a href="?status=pending&search=<?= $search ?>" class="btn-sm <?= $status === 'pending' ? 'active' : '' ?>">Pendentes</a>
                    <a href="?status=paid&search=<?= $search ?>" class="btn-sm <?= $status === 'paid' ? 'active' : '' ?>">Pagos</a>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <form method="GET" style="display:flex;gap:6px;">
                        <input type="text" name="search" placeholder="Buscar funcionário ou mês..." value="<?= $search ?>" style="padding:6px 12px;border:1px solid var(--border);border-radius:6px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;font-size:0.82rem;width:220px;">
                        <button type="submit" class="btn-sm" style="background:var(--primary);color:#000;border:none;"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="?action=create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Recibo</a>
                </div>
            </div>
            <div class="table-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>Período</th>
                            <th>Salário</th>
                            <th>Bónus</th>
                            <th>Deduções</th>
                            <th>Líquido</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payslips)): ?>
                            <tr><td colspan="9" class="empty-state">Nenhum recibo encontrado</td></tr>
                        <?php else: ?>
                            <?php foreach ($payslips as $ps): ?>
                                <tr>
                                    <td><strong><?= sanitize($ps['employee_name']) ?></strong></td>
                                    <td><?= sanitize($ps['month_year']) ?></td>
                                    <td>Kz <?= number_format($ps['salary'], 0, ',', ' ') ?></td>
                                    <td>Kz <?= number_format($ps['bonus'], 0, ',', ' ') ?></td>
                                    <td>Kz <?= number_format($ps['deductions'], 0, ',', ' ') ?></td>
                                    <td><strong>Kz <?= number_format($ps['net_salary'], 0, ',', ' ') ?></strong></td>
                                    <td><?= statusBadge($ps['status']) ?></td>
                                    <td><?= formatDate($ps['generated_at'] ?: $ps['created_at'], 'd/m/Y') ?></td>
                                    <td class="actions">
                                        <a href="?action=view&id=<?= $ps['id'] ?>" class="btn-icon" title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="payslip-pdf.php?id=<?= $ps['id'] ?>" class="btn-icon" title="PDF" target="_blank"><i class="fas fa-file-pdf"></i></a>
                                        <?php if ($ps['status'] === 'pending'): ?>
                                            <a href="?action=mark-paid&id=<?= $ps['id'] ?>" class="btn-icon" title="Marcar Pago" onclick="return confirm('Marcar como pago?')"><i class="fas fa-check-circle"></i></a>
                                        <?php endif; ?>
                                        <?php if (isAdmin()): ?>
                                            <a href="?action=delete&id=<?= $ps['id'] ?>" class="btn-icon danger" title="Eliminar" onclick="return confirm('Eliminar recibo?')"><i class="fas fa-trash"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if ($total > $perPage): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                            <a href="?page=<?= $i ?>&status=<?= $status ?>&search=<?= $search ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
