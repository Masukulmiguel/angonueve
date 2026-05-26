<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
checkSessionTimeout();

$stats = getDashboardStats();

$currentMonth = date('Y-m');
$revenueStats = getRevenueStats($currentMonth . '-01', date('Y-m-t'));
$recentMessages = db()->fetchAll("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");
$recentOrders = db()->fetchAll("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recentPayments = db()->fetchAll("SELECT * FROM payments WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5");
$abandonedData = getAbandonedOrders(2);

// notification counts
$notifPendingOrders = $stats['pending_orders'];
$notifPendingPayments = $stats['pending_payments'];
$notifUnreadChat = db()->count('support_chat', "is_read = 0 AND sender_type = 'client'");
$notifUnreadMessages = $stats['unread_messages'];
$notifAbandoned = $abandonedData['total_abandoned'];

$todayVisits = db()->fetchAll(
    "SELECT page_visited, COUNT(*) as visits FROM visitors WHERE DATE(visited_at) = CURDATE() GROUP BY page_visited ORDER BY visits DESC LIMIT 10"
);
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ANGONUEVE CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .stat-icon.red { background: rgba(255,82,82,0.1); color: var(--danger); }
        .stat-icon.teal { background: rgba(0,230,200,0.1); color: #00e6c8; }
        .stat-icon.pink { background: rgba(255,64,129,0.1); color: #ff4081; }
        .notif-panel { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .notif-header { display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-weight: 600; margin-bottom: 14px; }
        .notif-header i { color: var(--warning); }
        .notif-count { background: var(--danger); color: #fff; border-radius: 20px; padding: 1px 10px; font-size: 0.72rem; font-weight: 700; margin-left: auto; }
        .notif-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; }
        .notif-card { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 8px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); text-decoration: none; color: var(--text); transition: all 0.2s; }
        .notif-card:hover { border-color: var(--primary); background: rgba(0,212,255,0.03); }
        .notif-card.empty { opacity: 0.4; }
        .notif-card .notif-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0; }
        .notif-icon.orange { background: rgba(255,171,0,0.12); color: var(--warning); }
        .notif-icon.red { background: rgba(255,82,82,0.12); color: var(--danger); }
        .notif-icon.blue { background: rgba(0,212,255,0.12); color: var(--primary); }
        .notif-icon.purple { background: rgba(139,92,246,0.12); color: #8b5cf6; }
        .notif-icon.pink { background: rgba(255,64,129,0.12); color: #ff4081; }
        .notif-info { font-size: 0.78rem; line-height: 1.3; }
        .notif-info strong { font-size: 0.9rem; display: inline; }
        .notif-empty { text-align: center; padding: 20px; color: var(--text-muted); font-size: 0.85rem; }
        .notif-empty i { font-size: 1.5rem; display: block; margin-bottom: 8px; color: var(--success); }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search"><i class="fas fa-th-large"></i> <span>Dashboard</span></div>
                <div class="header-user"><span><?= $user['name'] ?></span><a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a></div>
            </div>
            <div class="admin-content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fas fa-eye"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['total_visitors'] ?></h3>
                            <p>Total Visitas</p>
                            <small>+<?= $stats['today_visitors'] ?> hoje</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-envelope"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['total_messages'] ?></h3>
                            <p>Mensagens</p>
                            <small><?= $stats['unread_messages'] ?> não lidas</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="fas fa-shopping-cart"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['total_orders'] ?></h3>
                            <p>Encomendas</p>
                            <small><?= $stats['pending_orders'] ?> pendentes</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="fas fa-cogs"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['total_services'] ?></h3>
                            <p>Serviços</p>
                            <small>disponíveis</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon teal"><i class="fas fa-file-invoice"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['total_invoices'] ?></h3>
                            <p>Facturas</p>
                            <small><?= $stats['pending_invoices'] ?> pendentes</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-credit-card"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['total_payments'] ?></h3>
                            <p>Pagamentos</p>
                            <small><?= $stats['pending_payments'] ?> por confirmar</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon pink"><i class="fas fa-chart-line"></i></div>
                        <div class="stat-info">
                            <h3>Kz <?= number_format($revenueStats['total_revenue'], 0, ',', ' ') ?></h3>
                            <p>Receita (mês)</p>
                            <small><a href="revenue.php" style="color:var(--primary);">Ver detalhes</a></small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red"><i class="fas fa-cart-arrow-down"></i></div>
                        <div class="stat-info">
                            <h3><?= $abandonedData['total_abandoned'] ?></h3>
                            <p>Abandonadas</p>
                            <small>Kz <?= number_format($abandonedData['abandoned_value'], 0, ',', ' ') ?></small>
                        </div>
                    </div>
                </div>

                <div id="notifPanel" class="notif-panel">
                    <?php if ($notifPendingOrders || $notifPendingPayments || $notifUnreadChat || $notifUnreadMessages || $notifAbandoned): ?>
                        <div class="notif-header"><i class="fas fa-bell"></i> Notificações <span class="notif-count" id="notifTotal"><?= $notifPendingOrders + $notifPendingPayments + $notifUnreadChat + $notifUnreadMessages + $notifAbandoned ?></span></div>
                        <div class="notif-grid" id="notifGrid">
                            <a href="orders.php?status=pending" class="notif-card <?= $notifPendingOrders ? '' : 'empty' ?>">
                                <div class="notif-icon orange"><i class="fas fa-shopping-cart"></i></div>
                                <div class="notif-info"><strong id="notifOrders"><?= $notifPendingOrders ?></strong> Encomendas pendentes</div>
                            </a>
                            <a href="payments.php?status=pending" class="notif-card <?= $notifPendingPayments ? '' : 'empty' ?>">
                                <div class="notif-icon red"><i class="fas fa-credit-card"></i></div>
                                <div class="notif-info"><strong id="notifPayments"><?= $notifPendingPayments ?></strong> Pagamentos por confirmar</div>
                            </a>
                            <a href="support-chat.php" class="notif-card <?= $notifUnreadChat ? '' : 'empty' ?>">
                                <div class="notif-icon blue"><i class="fas fa-comments"></i></div>
                                <div class="notif-info"><strong id="notifChat"><?= $notifUnreadChat ?></strong> Chat mensagens não lidas</div>
                            </a>
                            <a href="messages.php" class="notif-card <?= $notifUnreadMessages ? '' : 'empty' ?>">
                                <div class="notif-icon purple"><i class="fas fa-envelope"></i></div>
                                <div class="notif-info"><strong id="notifMessages"><?= $notifUnreadMessages ?></strong> Contacto mensagens não lidas</div>
                            </a>
                            <a href="abandoned.php" class="notif-card <?= $notifAbandoned ? '' : 'empty' ?>">
                                <div class="notif-icon pink"><i class="fas fa-cart-arrow-down"></i></div>
                                <div class="notif-info"><strong id="notifAbandoned"><?= $notifAbandoned ?></strong> Compras abandonadas</div>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="notif-header"><i class="fas fa-bell"></i> Notificações <span class="notif-count" id="notifTotal">0</span></div>
                        <div class="notif-empty"><i class="fas fa-check-circle"></i> Nenhuma notificação pendente</div>
                    <?php endif; ?>
                </div>

                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-envelope"></i> Mensagens Recentes</h3>
                            <a href="messages.php" class="btn-sm">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentMessages)): ?>
                                <p class="empty-state">Nenhuma mensagem recebida</p>
                            <?php else: ?>
                                <?php foreach ($recentMessages as $msg): ?>
                                    <div class="list-item <?= $msg['status'] === 'unread' ? 'unread' : '' ?>">
                                        <div class="list-info">
                                            <strong><?= sanitize($msg['name']) ?></strong>
                                            <p><?= substr(sanitize($msg['message']), 0, 60) ?>...</p>
                                        </div>
                                        <div class="list-meta">
                                            <?= statusBadge($msg['status']) ?>
                                            <small><?= timeAgo($msg['created_at']) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-shopping-cart"></i> Encomendas Recentes</h3>
                            <a href="orders.php" class="btn-sm">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentOrders)): ?>
                                <p class="empty-state">Nenhuma encomenda recebida</p>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <div class="list-item">
                                        <div class="list-info">
                                            <strong><?= sanitize($order['customer_name']) ?></strong>
                                            <p><?= sanitize($order['service_name']) ?> - <?= sanitize($order['plan_name']) ?></p>
                                        </div>
                                        <div class="list-meta">
                                            <?= statusBadge($order['status']) ?>
                                            <small><?= timeAgo($order['created_at']) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-credit-card"></i> Pagamentos Pendentes</h3>
                            <a href="payments.php?status=pending" class="btn-sm">Ver todos</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentPayments)): ?>
                                <p class="empty-state">Nenhum pagamento pendente</p>
                            <?php else: ?>
                                <?php foreach ($recentPayments as $p): ?>
                                    <div class="list-item unread">
                                        <div class="list-info">
                                            <strong><?= sanitize($p['client_name']) ?></strong>
                                            <p><?= sanitize($p['invoice_no']) ?> — Kz <?= number_format($p['amount'], 0, ',', ' ') ?></p>
                                        </div>
                                        <div class="list-meta">
                                            <span class="badge badge-warning"><?= strtoupper($p['method']) ?></span>
                                            <small><?= timeAgo($p['created_at']) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar"></i> Páginas mais visitadas hoje</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($todayVisits)): ?>
                                <p class="empty-state">Nenhuma visita hoje</p>
                            <?php else: ?>
                                <?php foreach ($todayVisits as $v): ?>
                                    <div class="list-item">
                                        <div class="list-info">
                                            <p style="font-size:0.85rem;">
                                                <?php
                                                    $page = basename($v['page_visited']);
                                                    echo $page ?: 'Página inicial';
                                                ?>
                                            </p>
                                        </div>
                                        <div class="list-meta">
                                            <span class="badge badge-primary"><?= $v['visits'] ?> visitas</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> Receita Mensal</h3>
                            <a href="revenue.php" class="btn-sm">Ver mais</a>
                        </div>
                        <div class="card-body">
                            <?php
                            $monthlyRev = $revenueStats['monthly'];
                            if (empty($monthlyRev)): ?>
                                <p class="empty-state">Nenhuma receita este mês</p>
                            <?php else:
                                $rev = $monthlyRev[0];
                            ?>
                                <div class="list-item">
                                    <div class="list-info">
                                        <strong>Total do mês</strong>
                                        <p><?= $rev['count'] ?> facturas pagas</p>
                                    </div>
                                    <div class="list-meta">
                                        <strong style="color:var(--success);font-size:1.1rem;">Kz <?= number_format($rev['total'], 0, ',', ' ') ?></strong>
                                    </div>
                                </div>
                                <?php if (count($monthlyRev) > 1):
                                    $prev = $monthlyRev[1];
                                    $diff = $prev['total'] > 0 ? round((($rev['total'] - $prev['total']) / $prev['total']) * 100) : 0;
                                    $arrow = $diff >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                                    $color = $diff >= 0 ? 'var(--success)' : 'var(--danger)';
                                ?>
                                    <div class="list-item">
                                        <div class="list-info">
                                            <p style="font-size:0.85rem;">Comparado a <?= date('M', strtotime($prev['month'] . '-01')) ?></p>
                                        </div>
                                        <div class="list-meta">
                                            <small style="color:<?= $color ?>;"><i class="fas <?= $arrow ?>"></i> <?= abs($diff) ?>%</small>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-cart-arrow-down"></i> Compras Abandonadas</h3>
                            <a href="abandoned.php" class="btn-sm">Gerir</a>
                        </div>
                        <div class="card-body">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:16px 20px;">
                                <div style="text-align:center;padding:16px;border-radius:8px;background:rgba(255,82,82,0.05);">
                                    <div style="font-size:1.5rem;font-weight:800;color:var(--danger);"><?= $abandonedData['total_abandoned'] ?></div>
                                    <small style="color:var(--text-muted);">Abandonadas</small>
                                </div>
                                <div style="text-align:center;padding:16px;border-radius:8px;background:rgba(255,171,0,0.05);">
                                    <div style="font-size:1.5rem;font-weight:800;color:var(--warning);">Kz <?= number_format($abandonedData['abandoned_value'], 0, ',', ' ') ?></div>
                                    <small style="color:var(--text-muted);">Valor Perdido</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="js/admin.js"></script>
    <script>
    // Notification polling every 30s
    const NOTIF_API = '../api/notifications.php';
    async function refreshNotifs() {
        try {
            const res = await fetch(NOTIF_API);
            const data = await res.json();
            if (!data.notifications) return;
            const total = document.getElementById('notifTotal');
            if (total) total.textContent = data.total;
            data.notifications.forEach(n => {
                const el = document.getElementById('notif' + n.type.charAt(0).toUpperCase() + n.type.slice(1));
                if (el) el.textContent = n.count;
            });
            const grid = document.getElementById('notifGrid');
            if (grid) {
                const cards = grid.querySelectorAll('.notif-card');
                cards.forEach(c => {
                    const strong = c.querySelector('strong');
                    const count = strong ? parseInt(strong.textContent) : 0;
                    c.classList.toggle('empty', count === 0);
                });
            }
        } catch(e) {}
    }
    setInterval(refreshNotifs, 30000);
    </script>
</body>
</html>
