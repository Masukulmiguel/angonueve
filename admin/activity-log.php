<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('activity_log');

$user = currentUser();

$export = $_GET['export'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

$actionFilter = sanitize($_GET['action_type'] ?? '');
$userSearch = sanitize($_GET['user_search'] ?? '');
$dateFrom = sanitize($_GET['date_from'] ?? '');
$dateTo = sanitize($_GET['date_to'] ?? '');
$textSearch = sanitize($_GET['text_search'] ?? '');

$where = [];
$params = [];

if ($actionFilter) {
    $where[] = 'al.action = ?';
    $params[] = $actionFilter;
}
if ($userSearch) {
    $where[] = '(u.name LIKE ? OR u.email LIKE ? OR al.user_id = ?)';
    $params[] = "%{$userSearch}%";
    $params[] = "%{$userSearch}%";
    $params[] = is_numeric($userSearch) ? (int)$userSearch : 0;
}
if ($dateFrom) {
    $where[] = 'al.created_at >= ?';
    $params[] = $dateFrom . ' 00:00:00';
}
if ($dateTo) {
    $where[] = 'al.created_at <= ?';
    $params[] = $dateTo . ' 23:59:59';
}
if ($textSearch) {
    $where[] = 'al.description LIKE ?';
    $params[] = "%{$textSearch}%";
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countSql = "SELECT COUNT(*) FROM activity_log al LEFT JOIN users u ON al.user_id = u.id {$whereClause}";
$total = db()->fetchOne($countSql, $params);
$total = $total ? reset($total) : 0;

$sql = "SELECT al.*, u.name AS user_name, u.email AS user_email
        FROM activity_log al
        LEFT JOIN users u ON al.user_id = u.id
        {$whereClause}
        ORDER BY al.created_at DESC
        LIMIT ? OFFSET ?";
$queryParams = array_merge($params, [$perPage, $offset]);
$logs = db()->fetchAll($sql, $queryParams);

if ($export === 'csv') {
    $headers = ['ID', 'Usuário', 'Email', 'Acção', 'Descrição', 'IP', 'Data'];
    $rows = [];
    foreach ($logs as $log) {
        $rows[] = [
            $log['id'],
            $log['user_name'] ?? '—',
            $log['user_email'] ?? '—',
            $log['action'],
            $log['description'] ?? '',
            $log['ip_address'] ?? '—',
            $log['created_at']
        ];
    }
    exportCSV($headers, $rows, 'registro-actividades.csv');
}

$actionOptions = ['login', 'logout', 'create', 'update', 'delete', 'confirm', 'cancel', 'send'];
$hasFilters = $actionFilter || $userSearch || $dateFrom || $dateTo || $textSearch;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo de Actividades - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <div class="header-search"><i class="fas fa-history"></i> <span>Registo de Actividades</span></div>
            <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
        </div>
        <div class="admin-content">
            <div class="table-controls">
                <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;padding:12px 0;">
                    <select name="action_type" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;font-size:0.85rem;">
                        <option value="">Todas as Acções</option>
                        <?php foreach ($actionOptions as $opt): ?>
                            <option value="<?= $opt ?>" <?= $actionFilter === $opt ? 'selected' : '' ?>><?= ucfirst($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="user_search" placeholder="Usuário ou email" value="<?= sanitize($userSearch) ?>" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;font-size:0.85rem;width:180px;">
                    <input type="date" name="date_from" value="<?= sanitize($dateFrom) ?>" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;font-size:0.85rem;">
                    <input type="date" name="date_to" value="<?= sanitize($dateTo) ?>" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;font-size:0.85rem;">
                    <input type="text" name="text_search" placeholder="Buscar na descrição" value="<?= sanitize($textSearch) ?>" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;background:rgba(255,255,255,0.03);color:var(--text);font-family:'Inter',sans-serif;font-size:0.85rem;width:200px;">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filtrar</button>
                    <?php if ($hasFilters): ?>
                        <a href="activity-log.php" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Limpar Filtros</a>
                    <?php endif; ?>
                    <a href="?export=csv&action_type=<?= urlencode($actionFilter) ?>&user_search=<?= urlencode($userSearch) ?>&date_from=<?= urlencode($dateFrom) ?>&date_to=<?= urlencode($dateTo) ?>&text_search=<?= urlencode($textSearch) ?>" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Exportar CSV</a>
                </form>
            </div>
            <div class="table-card">
                <div style="padding:12px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-list"></i>
                    <strong>Registo de Actividades</strong>
                    <span style="color:var(--text-muted);font-size:0.8rem;">— Total: <?= $total ?> registo(s)</span>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuário</th>
                            <th>Acção</th>
                            <th>Descrição</th>
                            <th>IP</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="6" class="empty-state">Nenhum registo de actividade encontrado</td></tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= $log['id'] ?></td>
                                    <td><strong><?= sanitize($log['user_name'] ?? '—') ?></strong></td>
                                    <td><?= statusBadge($log['action']) ?></td>
                                    <td><?= sanitize($log['description'] ?: '—') ?></td>
                                    <td><code style="font-size:0.8rem;"><?= sanitize($log['ip_address'] ?: '—') ?></code></td>
                                    <td><?= formatDate($log['created_at'], 'd/m/Y H:i') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if ($total > $perPage): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                            <a href="?page=<?= $i ?>&action_type=<?= urlencode($actionFilter) ?>&user_search=<?= urlencode($userSearch) ?>&date_from=<?= urlencode($dateFrom) ?>&date_to=<?= urlencode($dateTo) ?>&text_search=<?= urlencode($textSearch) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
