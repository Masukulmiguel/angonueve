<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
requirePermission('employees');

$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);

$allPerms = getAllPermissions();
$error = '';
$success = '';

// --- DELETE ---
if (isset($_GET['delete'])) {
    if (!isAdmin()) {
        die('Apenas administradores podem eliminar');
    }
    $delId = intval($_GET['delete']);
    if ($user['id'] === $delId) {
        $error = 'Não pode eliminar a si mesmo.';
    } else {
        $target = db()->fetchOne("SELECT id, name, photo FROM users WHERE id = ? AND role = 'employee'", [$delId]);
        if ($target) {
            if ($target['photo'] && file_exists(UPLOAD_DIR . '/employees/' . $target['photo'])) {
                unlink(UPLOAD_DIR . '/employees/' . $target['photo']);
            }
            db()->query("DELETE FROM permissions WHERE user_id = ?", [$delId]);
            db()->query("DELETE FROM users WHERE id = ?", [$delId]);
            logActivity($user['id'], 'delete_employee', "Funcionário #{$delId} {$target['name']} eliminado");
            $success = 'Funcionário eliminado com sucesso!';
        }
    }
}

// --- ADD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    require_csrf();
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $position = sanitize($_POST['position'] ?? '');
    $salary = floatval($_POST['salary'] ?? 0);
    $functionDesc = sanitize($_POST['function_desc'] ?? '');
    $hireDate = sanitize($_POST['hire_date'] ?? '');
    $perms = $_POST['permissions'] ?? [];

    if (!$name || !$email || !$password) {
        $error = 'Nome, email e palavra-passe são obrigatórios.';
    } elseif (db()->fetchOne("SELECT id FROM users WHERE email = ?", [$email])) {
        $error = 'Email já registado.';
    } else {
        $photo = null;
        if (!empty($_FILES['photo']['name'])) {
            $up = uploadEmployeePhoto($_FILES['photo']);
            if (isset($up['error'])) { $error = $up['error']; }
            else { $photo = $up['filename']; }
        }
        if (!$error) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $uid = db()->insert('users', [
                'name' => $name, 'email' => $email, 'phone' => $phone,
                'password' => $hashed, 'role' => 'employee', 'status' => 'active',
                'photo' => $photo, 'position' => $position,
                'salary' => $salary > 0 ? $salary : null,
                'function_desc' => $functionDesc, 'hire_date' => $hireDate ?: null
            ]);
            setEmployeePermissions($uid, $perms);
            logActivity($user['id'], 'add_employee', "Funcionário #{$uid} {$name} criado");
            $success = 'Funcionário adicionado com sucesso!';
        }
    }
}

// --- EDIT ---
$editEmployee = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editEmployee = db()->fetchOne("SELECT * FROM users WHERE id = ? AND role = 'employee'", [$editId]);
    if ($editEmployee) {
        $editEmployee['permissions'] = getEmployeePermissions($editId);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    require_csrf();
    $editId = intval($_POST['id']);
    $target = db()->fetchOne("SELECT id, photo FROM users WHERE id = ? AND role = 'employee'", [$editId]);
    if (!$target) { $error = 'Funcionário não encontrado.'; }
    else {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $position = sanitize($_POST['position'] ?? '');
        $salary = floatval($_POST['salary'] ?? 0);
        $functionDesc = sanitize($_POST['function_desc'] ?? '');
        $hireDate = sanitize($_POST['hire_date'] ?? '');
        $perms = $_POST['permissions'] ?? [];

        if (!$name || !$email) { $error = 'Nome e email são obrigatórios.'; }
        else {
            $existing = db()->fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $editId]);
            if ($existing) { $error = 'Email já usado por outro utilizador.'; }
        }

        if (!$error) {
            $photo = $target['photo'];
            if (!empty($_FILES['photo']['name'])) {
                $up = uploadEmployeePhoto($_FILES['photo']);
                if (isset($up['error'])) { $error = $up['error']; }
                else {
                    if ($photo && file_exists(UPLOAD_DIR . '/employees/' . $photo)) {
                        unlink(UPLOAD_DIR . '/employees/' . $photo);
                    }
                    $photo = $up['filename'];
                }
            }
        }

        if (!$error) {
            $updateData = [
                'name' => $name, 'email' => $email, 'phone' => $phone,
                'photo' => $photo, 'position' => $position,
                'salary' => $salary > 0 ? $salary : null,
                'function_desc' => $functionDesc,
                'hire_date' => $hireDate ?: null
            ];
            if ($password) {
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $updateData['updated_at'] = date('Y-m-d H:i:s');
            db()->update('users', $updateData, 'id = :id', ['id' => $editId]);
            setEmployeePermissions($editId, $perms);
            logActivity($user['id'], 'edit_employee', "Funcionário #{$editId} {$name} actualizado");
            $success = 'Funcionário actualizado com sucesso!';
            $editEmployee = null;
        }
    }
}

$employees = db()->fetchAll("SELECT * FROM users WHERE role = 'employee' ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .emp-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        .emp-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; transition: all 0.3s; }
        .emp-card:hover { border-color: var(--primary); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .emp-cover { height: 80px; background: linear-gradient(135deg, #0d1f3c, #162d50); position: relative; }
        .emp-avatar { width: 64px; height: 64px; border-radius: 50%; border: 3px solid var(--card-bg); position: absolute; bottom: -32px; left: 20px; object-fit: cover; background: var(--bg); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--text-muted); }
        .emp-body { padding: 40px 20px 16px; }
        .emp-body h3 { font-size: 1rem; font-weight: 600; margin: 0 0 2px; }
        .emp-body .emp-position { font-size: 0.78rem; color: var(--text-muted); margin: 0 0 10px; }
        .emp-details { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
        .emp-details span { font-size: 0.72rem; padding: 3px 10px; border-radius: 20px; background: rgba(255,255,255,0.04); border: 1px solid var(--border); }
        .emp-actions { display: flex; gap: 8px; border-top: 1px solid var(--border); padding: 12px 20px; }
        .emp-actions a { font-size: 0.78rem; }
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; width: 90%; max-width: 680px; max-height: 90vh; overflow-y: auto; padding: 24px; }
        .modal h2 { font-size: 1.1rem; margin: 0 0 16px; }
        .modal-close { float: right; background: none; border: none; color: var(--text-muted); font-size: 1.2rem; cursor: pointer; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 0.82rem; color: var(--text-muted); margin-bottom: 4px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 6px; background: rgba(255,255,255,0.02); color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.88rem; outline: none; box-sizing: border-box; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--primary); }
        .form-group textarea { resize: vertical; min-height: 60px; }
        .perm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 6px; margin: 8px 0 14px; }
        .perm-grid label { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; padding: 6px 8px; border: 1px solid var(--border); border-radius: 6px; cursor: pointer; transition: all 0.2s; }
        .perm-grid label:hover { border-color: var(--primary); }
        .perm-grid input:checked + span { color: var(--primary); font-weight: 600; }
        .photo-preview { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); margin-bottom: 8px; }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,212,255,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; }
        .empty-state i { font-size: 2.5rem; color: var(--text-muted); margin-bottom: 12px; opacity: 0.3; display: block; }
        .emp-stat { text-align: center; padding: 20px; background: rgba(0,212,255,0.04); border: 1px dashed var(--border); border-radius: 8px; margin-bottom: 20px; }
        .emp-stat strong { font-size: 1.8rem; display: block; }
        .emp-stat small { color: var(--text-muted); font-size: 0.82rem; }
        @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-users-cog"></i> <span>Funcionários</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content" style="padding:16px 24px;">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success" id="successMsg"><?= $success ?></div>
            <?php endif; ?>

            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
                <div class="emp-stat" style="flex:1;text-align:left;border:none;padding:0;">
                    <strong><?= count($employees) ?></strong>
                    <small>Funcionários registados</small>
                </div>
                <button class="btn btn-primary" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Novo Funcionário</button>
            </div>

            <?php if (empty($employees)): ?>
                <p class="empty-state" style="text-align:center;padding:40px;">
                    <i class="fas fa-users"></i>
                    Nenhum funcionário registado<br>
                    <small style="color:var(--text-muted);">Clique em "Novo Funcionário" para adicionar.</small>
                </p>
            <?php else: ?>
                <div class="emp-grid">
                    <?php foreach ($employees as $emp): ?>
                        <?php $perms = getEmployeePermissions($emp['id']); ?>
                        <div class="emp-card">
                            <div class="emp-cover">
                                <?php if ($emp['photo']): ?>
                                    <img src="../uploads/employees/<?= $emp['photo'] ?>" class="emp-avatar" alt="<?= sanitize($emp['name']) ?>">
                                <?php else: ?>
                                    <div class="emp-avatar"><i class="fas fa-user"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="emp-body">
                                <h3><?= sanitize($emp['name']) ?></h3>
                                <p class="emp-position"><?= $emp['position'] ? sanitize($emp['position']) : 'Sem cargo definido' ?></p>
                                <div class="emp-details">
                                    <span><i class="fas fa-envelope"></i> <?= sanitize($emp['email']) ?></span>
                                    <?php if ($emp['phone']): ?><span><i class="fas fa-phone"></i> <?= sanitize($emp['phone']) ?></span><?php endif; ?>
                                    <?php if ($emp['salary']): ?><span><i class="fas fa-money-bill"></i> Kz <?= number_format($emp['salary'], 0, ',', ' ') ?></span><?php endif; ?>
                                    <?php if ($emp['hire_date']): ?><span><i class="fas fa-calendar"></i> Desde <?= formatDate($emp['hire_date'], 'd/m/Y') ?></span><?php endif; ?>
                                    <span><i class="fas fa-key"></i> <?= count($perms) ?> permissões</span>
                                </div>
                                <?php if ($emp['function_desc']): ?>
                                    <p style="font-size:0.78rem;color:var(--text-muted);line-height:1.5;margin:6px 0 0;"><?= nl2br(sanitize($emp['function_desc'])) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="emp-actions">
                                <a href="?edit=<?= $emp['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Editar</a>
                                <a href="?view=<?= $emp['id'] ?>" class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i> Ver</a>
                                <a href="?delete=<?= $emp['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminar <?= sanitize(addslashes($emp['name'])) ?>? Esta ação não pode ser desfeita.')"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- ADD MODAL -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
        <h2><i class="fas fa-user-plus"></i> Novo Funcionário</h2>
        <form method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <div class="form-row">
                <div class="form-group">
                    <label>Nome completo *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Palavra-passe *</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="phone">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Cargo</label>
                    <input type="text" name="position" placeholder="Ex: Técnico de Redes">
                </div>
                <div class="form-group">
                    <label>Salário (Kz)</label>
                    <input type="number" name="salary" step="0.01" min="0">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Data de Contratação</label>
                    <input type="date" name="hire_date">
                </div>
                <div class="form-group">
                    <label>Foto de Perfil</label>
                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif,.webp">
                </div>
            </div>
            <div class="form-group">
                <label>Função / Descrição</label>
                <textarea name="function_desc" rows="3" placeholder="Descreva as funções do funcionário..."></textarea>
            </div>
            <div class="form-group">
                <label>Permissões de Acesso</label>
                <div class="perm-grid">
                    <?php foreach ($allPerms as $key => $label): ?>
                        <label><input type="checkbox" name="permissions[]" value="<?= $key ?>"> <span><?= $label ?></span></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<?php if ($editEmployee): ?>
<div class="modal-overlay active" id="editModal">
    <div class="modal">
        <button class="modal-close" onclick="window.location.href='employees.php'">&times;</button>
        <h2><i class="fas fa-user-edit"></i> Editar: <?= sanitize($editEmployee['name']) ?></h2>
        <form method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $editEmployee['id'] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Nome completo *</label>
                    <input type="text" name="name" value="<?= sanitize($editEmployee['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= sanitize($editEmployee['email']) ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nova palavra-passe (deixe vazio para manter)</label>
                    <input type="password" name="password" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="phone" value="<?= sanitize($editEmployee['phone'] ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Cargo</label>
                    <input type="text" name="position" value="<?= sanitize($editEmployee['position'] ?? '') ?>" placeholder="Ex: Técnico de Redes">
                </div>
                <div class="form-group">
                    <label>Salário (Kz)</label>
                    <input type="number" name="salary" step="0.01" min="0" value="<?= $editEmployee['salary'] ?? '' ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Data de Contratação</label>
                    <input type="date" name="hire_date" value="<?= $editEmployee['hire_date'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label>Foto de Perfil</label>
                    <?php if ($editEmployee['photo']): ?>
                        <div><img src="../uploads/employees/<?= $editEmployee['photo'] ?>" class="photo-preview"></div>
                    <?php endif; ?>
                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif,.webp">
                </div>
            </div>
            <div class="form-group">
                <label>Função / Descrição</label>
                <textarea name="function_desc" rows="3" placeholder="Descreva as funções do funcionário..."><?= sanitize($editEmployee['function_desc'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Permissões de Acesso</label>
                <div class="perm-grid">
                    <?php foreach ($allPerms as $key => $label): ?>
                        <label><input type="checkbox" name="permissions[]" value="<?= $key ?>" <?= in_array($key, $editEmployee['permissions'] ?? []) ? 'checked' : '' ?>> <span><?= $label ?></span></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
            <a href="employees.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- VIEW MODAL -->
<?php
if (isset($_GET['view'])) {
    $viewId = intval($_GET['view']);
    $viewEmp = db()->fetchOne("SELECT * FROM users WHERE id = ? AND role = 'employee'", [$viewId]);
    if ($viewEmp):
        $viewPerms = getEmployeePermissions($viewId);
?>
<div class="modal-overlay active" id="viewModal">
    <div class="modal" style="max-width:500px;">
        <button class="modal-close" onclick="window.location.href='employees.php'">&times;</button>
        <h2><i class="fas fa-user"></i> <?= sanitize($viewEmp['name']) ?></h2>
        <div style="text-align:center;margin-bottom:20px;">
            <?php if ($viewEmp['photo']): ?>
                <img src="../uploads/employees/<?= $viewEmp['photo'] ?>" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
            <?php else: ?>
                <div style="width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--text-muted);margin:0 auto;border:3px solid var(--border);"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <h3 style="margin:12px 0 4px;"><?= sanitize($viewEmp['name']) ?></h3>
            <p style="color:var(--text-muted);font-size:0.85rem;margin:0;"><?= $viewEmp['position'] ? sanitize($viewEmp['position']) : 'Sem cargo' ?></p>
        </div>
        <table class="admin-table" style="font-size:0.88rem;">
            <tr><td style="padding:8px 12px;color:var(--text-muted);">Email</td><td style="padding:8px 12px;"><?= sanitize($viewEmp['email']) ?></td></tr>
            <?php if ($viewEmp['phone']): ?><tr><td style="padding:8px 12px;color:var(--text-muted);">Telefone</td><td style="padding:8px 12px;"><?= sanitize($viewEmp['phone']) ?></td></tr><?php endif; ?>
            <?php if ($viewEmp['salary']): ?><tr><td style="padding:8px 12px;color:var(--text-muted);">Salário</td><td style="padding:8px 12px;">Kz <?= number_format($viewEmp['salary'], 0, ',', ' ') ?></td></tr><?php endif; ?>
            <?php if ($viewEmp['hire_date']): ?><tr><td style="padding:8px 12px;color:var(--text-muted);">Contratação</td><td style="padding:8px 12px;"><?= formatDate($viewEmp['hire_date'], 'd/m/Y') ?></td></tr><?php endif; ?>
            <tr><td style="padding:8px 12px;color:var(--text-muted);">Registo</td><td style="padding:8px 12px;"><?= formatDate($viewEmp['created_at'], 'd/m/Y H:i') ?></td></tr>
        </table>
        <?php if ($viewEmp['function_desc']): ?>
            <div style="margin-top:12px;padding:12px;background:rgba(255,255,255,0.02);border-radius:6px;">
                <strong style="font-size:0.82rem;color:var(--text-muted);">Função:</strong>
                <p style="font-size:0.85rem;margin:4px 0 0;line-height:1.6;"><?= nl2br(sanitize($viewEmp['function_desc'])) ?></p>
            </div>
        <?php endif; ?>
        <?php if ($viewPerms): ?>
            <div style="margin-top:12px;">
                <strong style="font-size:0.82rem;color:var(--text-muted);">Permissões:</strong>
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;">
                    <?php foreach ($viewPerms as $p): ?>
                        <span class="badge badge-primary"><?= $allPerms[$p] ?? $p ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div style="margin-top:20px;display:flex;gap:8px;">
            <a href="?edit=<?= $viewEmp['id'] ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
            <a href="employees.php" class="btn btn-secondary">Fechar</a>
        </div>
    </div>
</div>
<?php endif; } ?>

<script>
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
setTimeout(() => { const el = document.getElementById('successMsg'); if (el) el.style.display = 'none'; }, 3000);
</script>
</body>
</html>