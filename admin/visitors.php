<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();
requirePermission('visitors');

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = ITEMS_PER_PAGE;
$offset = ($page - 1) * $perPage;

$total = db()->count('visitors');
$visitors = db()->fetchAll("SELECT * FROM visitors ORDER BY visited_at DESC LIMIT ? OFFSET ?", [$perPage, $offset]);

$stats = [
    'total' => $total,
    'today' => db()->count('visitors', 'DATE(visited_at) = CURDATE()'),
    'unique_today' => db()->count('visitors', 'DATE(visited_at) = CURDATE() GROUP BY ip_address'),
    'desktop' => db()->count('visitors', "device = 'Desktop'"),
    'mobile' => db()->count('visitors', "device = 'Mobile'"),
];

$user = currentUser();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitantes - ANGONUEVE CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-eye"></i> <span>Visitantes</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
                    <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-globe"></i></div><div class="stat-info"><h3><?= $stats['total'] ?></h3><p>Total Visitas</p></div></div>
                    <div class="stat-card"><div class="stat-icon green"><i class="fas fa-calendar-day"></i></div><div class="stat-info"><h3><?= $stats['today'] ?></h3><p>Hoje</p></div></div>
                    <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-users"></i></div><div class="stat-info"><h3><?= $stats['unique_today'] ?></h3><p>Únicos Hoje</p></div></div>
                    <div class="stat-card"><div class="stat-icon orange"><i class="fas fa-mobile-alt"></i></div><div class="stat-info"><h3><?= $stats['mobile'] ?></h3><p>Mobile</p></div></div>
                </div>
                <div class="table-card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>IP</th>
                                <th>Página</th>
                                <th>Browser</th>
                                <th>OS</th>
                                <th>Dispositivo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($visitors)): ?>
                                <tr><td colspan="6" class="empty-state">Nenhum visitante registado</td></tr>
                            <?php else: ?>
                                <?php foreach ($visitors as $v): ?>
                                    <tr>
                                        <td><code><?= $v['ip_address'] ?></code></td>
                                        <td><small><?= basename($v['page_visited'] ?: 'index') ?></small></td>
                                        <td><?= $v['browser'] ?></td>
                                        <td><?= $v['os'] ?></td>
                                        <td><span class="badge badge-<?= $v['device'] === 'Mobile' ? 'warning' : 'primary' ?>"><?= $v['device'] ?></span></td>
                                        <td><small><?= timeAgo($v['visited_at']) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if ($total > $perPage): ?>
                        <div class="pagination">
                            <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
                                <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
