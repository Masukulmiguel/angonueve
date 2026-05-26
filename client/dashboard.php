<?php
require_once __DIR__ . '/../includes/auth.php';
requireClient();

$user = currentUser();
$email = $user['email'];

$totalOrders = db()->count('orders', 'customer_email = ?', [$email]);
$pendingOrders = db()->count('orders', "customer_email = ? AND status IN ('pending', 'confirmed')", [$email]);
$totalMessages = db()->count('messages', 'email = ?', [$email]);
$recentOrders = db()->fetchAll(
    "SELECT * FROM orders WHERE customer_email = ? ORDER BY created_at DESC LIMIT 5",
    [$email]
);
$recentMessages = db()->fetchAll(
    "SELECT * FROM messages WHERE email = ? ORDER BY created_at DESC LIMIT 5",
    [$email]
);
$invoices = getClientInvoices($user['id']);
$activeServices = db()->fetchAll(
    "SELECT * FROM client_services WHERE client_id = ? AND status = 'active' ORDER BY created_at DESC",
    [$user['id']]
);
$pendingInvoices = array_filter($invoices, fn($i) => $i['status'] === 'pending');
$unreadChat = db()->count('support_chat', "client_id = ? AND is_read = 0 AND sender_type = 'admin'", [$user['id']]);

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= sanitize($user['name']) ?> | ANGONUEVE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/css/admin.css">
    <style>
        .client-sidebar .sidebar-brand i { color: var(--success); }
        .client-sidebar .sidebar-nav a.active { background: rgba(0,230,118,0.1); color: var(--success); }
        .client-sidebar .sidebar-nav a:hover { color: var(--success); }
        .welcome-card { background: linear-gradient(135deg, #0d1f3c, #0a2818); border: 1px solid var(--border); border-radius: 12px; padding: 32px; margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
        .welcome-card h1 { font-size: 1.6rem; }
        .welcome-card h1 span { color: var(--success); }
        .welcome-card p { color: var(--text-muted); font-size: 0.95rem; margin-top: 8px; }
        .welcome-avatar { width: 72px; height: 72px; border-radius: 50%; background: rgba(0,230,118,0.1); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--success); border: 2px solid rgba(0,230,118,0.2); flex-shrink: 0; }
        .quick-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 32px; }
        .quick-action { display: flex; align-items: center; gap: 10px; padding: 14px 24px; border-radius: 10px; background: var(--card-bg); border: 1px solid var(--border); color: var(--text); font-size: 0.9rem; font-weight: 500; transition: all 0.3s; font-family: 'Inter', sans-serif; cursor: pointer; text-decoration: none; }
        .quick-action:hover { border-color: var(--success); color: var(--success); transform: translateY(-2px); }
        .quick-action i { font-size: 1.1rem; color: var(--success); }
        .stat-card.client-stat .stat-icon { background: rgba(0,230,118,0.1); color: var(--success); }
        .empty-state i { font-size: 2.5rem; color: var(--text-muted); margin-bottom: 12px; opacity: 0.3; display: block; }
        .badge-success { background: rgba(0,230,118,0.15); color: var(--success); }
        .header-user .avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(0,230,118,0.15); display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; color: var(--success); }
        .service-tag { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; background: rgba(0,230,118,0.1); color: var(--success); font-size: 0.8rem; font-weight: 500; }
        .pending-badge { background: rgba(255,171,0,0.15); color: var(--warning); }
        .notif-panel { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .notif-header { display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-weight: 600; margin-bottom: 14px; }
        .notif-header i { color: var(--warning); }
        .notif-count { background: var(--danger); color: #fff; border-radius: 20px; padding: 1px 10px; font-size: 0.72rem; font-weight: 700; margin-left: auto; }
        .notif-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; }
        .notif-card { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 8px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); text-decoration: none; color: var(--text); transition: all 0.2s; }
        .notif-card:hover { border-color: var(--success); background: rgba(0,230,118,0.03); }
        .notif-card.empty { opacity: 0.4; }
        .notif-card .notif-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0; }
        .notif-icon.red { background: rgba(255,82,82,0.12); color: var(--danger); }
        .notif-icon.orange { background: rgba(255,171,0,0.12); color: var(--warning); }
        .notif-icon.blue { background: rgba(0,212,255,0.12); color: var(--primary); }
        .notif-info { font-size: 0.78rem; line-height: 1.3; }
        .notif-info strong { font-size: 0.9rem; display: inline; }
        .notif-empty { text-align: center; padding: 20px; color: var(--text-muted); font-size: 0.85rem; }
        .notif-empty i { font-size: 1.5rem; display: block; margin-bottom: 8px; color: var(--success); }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar client-sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-user-circle"></i>
                <span>Cliente</span>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="services.php" class="<?= $currentPage === 'services.php' ? 'active' : '' ?>">
                    <i class="fas fa-concierge-bell"></i> Serviços
                </a>
                <a href="orders.php" class="<?= $currentPage === 'orders.php' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i> Encomendas
                </a>
                <a href="invoices.php" class="<?= $currentPage === 'invoices.php' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i> Facturas
                </a>
                <a href="chat.php" class="<?= $currentPage === 'chat.php' ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i> Chat Suporte
                </a>
                <hr>
                <a href="profile.php" class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-cog"></i> Meu Perfil
                </a>
                <a href="change-password.php" class="<?= $currentPage === 'change-password.php' ? 'active' : '' ?>">
                    <i class="fas fa-key"></i> Alterar Password
                </a>
                <hr>
                <a href="../index.html" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Ver Site
                </a>
                <a href="logout.php" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <div class="header-search">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </div>
                <div class="header-user">
                    <span class="avatar"><i class="fas fa-user"></i></span>
                    <span><?= sanitize($user['name']) ?></span>
                    <a href="logout.php" class="btn-sm"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            </div>

            <div class="admin-content">
                <div class="welcome-card">
                    <div>
                        <h1>Bem-vindo(a), <span><?= sanitize(explode(' ', $user['name'])[0]) ?></span>!</h1>
                        <p>Esta é a sua área pessoal. Aqui pode acompanhar os seus serviços, facturas e pagamentos.</p>
                    </div>
                    <div class="welcome-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="quick-actions">
                    <a href="orders.php" class="quick-action"><i class="fas fa-shopping-cart"></i> Minhas Encomendas</a>
                    <a href="invoices.php" class="quick-action"><i class="fas fa-file-invoice"></i> Facturas</a>
                    <a href="chat.php" class="quick-action"><i class="fas fa-comments"></i> Chat Suporte</a>
                    <a href="profile.php" class="quick-action"><i class="fas fa-user-cog"></i> Meu Perfil</a>
                    <a href="change-password.php" class="quick-action"><i class="fas fa-key"></i> Alterar Password</a>
                    <a href="../index.html" class="quick-action"><i class="fas fa-globe"></i> Ver Site</a>
                </div>

                <div class="stats-grid">
                    <div class="stat-card client-stat">
                        <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="stat-info">
                            <h3><?= $totalOrders ?></h3>
                            <p>Total Encomendas</p>
                        </div>
                    </div>
                    <div class="stat-card client-stat">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-info">
                            <h3><?= $pendingOrders ?></h3>
                            <p>Pendentes</p>
                        </div>
                    </div>
                    <div class="stat-card client-stat">
                        <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                        <div class="stat-info">
                            <h3><?= count($invoices) ?></h3>
                            <p>Facturas</p>
                            <small><?= count($pendingInvoices) ?> por pagar</small>
                        </div>
                    </div>
                    <div class="stat-card client-stat">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-info">
                            <h3><?= count($activeServices) ?></h3>
                            <p>Serviços Activos</p>
                        </div>
                    </div>
                </div>

                <?php
                $notifTotal = count($pendingInvoices) + $pendingOrders + $unreadChat;
                ?>
                <div id="notifPanel" class="notif-panel">
                    <?php if ($notifTotal): ?>
                        <div class="notif-header"><i class="fas fa-bell"></i> Notificações <span class="notif-count" id="notifTotal"><?= $notifTotal ?></span></div>
                        <div class="notif-grid" id="notifGrid">
                            <a href="invoices.php" class="notif-card <?= count($pendingInvoices) ? '' : 'empty' ?>">
                                <div class="notif-icon red"><i class="fas fa-file-invoice"></i></div>
                                <div class="notif-info"><strong id="notifInvoices"><?= count($pendingInvoices) ?></strong> Facturas por pagar</div>
                            </a>
                            <a href="orders.php" class="notif-card <?= $pendingOrders ? '' : 'empty' ?>">
                                <div class="notif-icon orange"><i class="fas fa-shopping-cart"></i></div>
                                <div class="notif-info"><strong id="notifOrders"><?= $pendingOrders ?></strong> Encomendas pendentes</div>
                            </a>
                            <a href="chat.php" class="notif-card <?= $unreadChat ? '' : 'empty' ?>">
                                <div class="notif-icon blue"><i class="fas fa-comments"></i></div>
                                <div class="notif-info"><strong id="notifChat"><?= $unreadChat ?></strong> Mensagens novas no chat</div>
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
                            <h3><i class="fas fa-cogs"></i> Meus Serviços Activos</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($activeServices)): ?>
                                <p class="empty-state"><i class="fas fa-box-open"></i>Nenhum serviço activo</p>
                                <a href="services.php" class="btn btn-success btn-sm" style="display:block;text-align:center;margin-top:12px;"><i class="fas fa-concierge-bell"></i> Ver Serviços Disponíveis</a>
                            <?php else: ?>
                                <?php foreach ($activeServices as $s): ?>
                                    <div class="list-item">
                                        <div class="list-info">
                                            <strong><?= sanitize($s['service_name']) ?></strong>
                                            <p><?= sanitize($s['plan_name'] ?: '—') ?></p>
                                        </div>
                                        <div class="list-meta">
                                            <span class="badge badge-success">Activo</span>
                                            <?php if ($s['expires_at']): ?>
                                                <small>Expira: <?= formatDate($s['expires_at'], 'd/m/Y') ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-shopping-cart"></i> Últimas Encomendas</h3>
                            <a href="orders.php" class="btn-sm">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentOrders)): ?>
                                <p class="empty-state"><i class="fas fa-box-open"></i>Nenhuma encomenda encontrada</p>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $o): ?>
                                    <div class="list-item">
                                        <div class="list-info">
                                            <strong><?= sanitize($o['service_name']) ?></strong>
                                            <p><?= sanitize($o['plan_name']) ?> — <?= formatDate($o['created_at'], 'd/m/Y') ?></p>
                                        </div>
                                        <div class="list-meta">
                                            <?= statusBadge($o['status']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-file-invoice"></i> Facturas Pendentes</h3>
                            <a href="invoices.php" class="btn-sm">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($pendingInvoices)): ?>
                                <p class="empty-state"><i class="fas fa-check-circle"></i>Nenhuma factura pendente</p>
                            <?php else: ?>
                                <?php foreach (array_slice($pendingInvoices, 0, 5) as $inv): ?>
                                    <div class="list-item" style="border-left:3px solid var(--warning);">
                                        <div class="list-info">
                                            <strong><?= sanitize($inv['invoice_no']) ?></strong>
                                            <p><?= sanitize($inv['service_name'] ?: 'Serviço') ?></p>
                                        </div>
                                        <div class="list-meta">
                                            <strong style="color:var(--warning);">Kz <?= number_format($inv['total'], 0, ',', ' ') ?></strong>
                                            <a href="invoice-view.php?id=<?= $inv['id'] ?>&pay=1" class="btn btn-success btn-sm" style="margin-top:4px;">Pagar</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-envelope"></i> Mensagens Enviadas</h3>
                            <a href="../contact.html" class="btn-sm">Nova</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentMessages)): ?>
                                <p class="empty-state"><i class="fas fa-comment-dots"></i>Nenhuma mensagem enviada</p>
                            <?php else: ?>
                                <?php foreach ($recentMessages as $m): ?>
                                    <div class="list-item">
                                        <div class="list-info">
                                            <strong><?= sanitize($m['subject'] ?: 'Sem assunto') ?></strong>
                                            <p><?= substr(sanitize($m['message']), 0, 60) ?>...</p>
                                        </div>
                                        <div class="list-meta">
                                            <?= statusBadge($m['status']) ?>
                                            <small><?= timeAgo($m['created_at']) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-info-circle"></i> Precisa de Ajuda?</h3>
                        </div>
                        <div class="card-body" style="padding: 20px;">
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 16px; line-height: 1.7;">
                                Estamos sempre disponíveis para ajudar. Entre em contacto connosco através dos canais abaixo.
                            </p>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <a href="tel:+244935603163" class="quick-action" style="justify-content: center;">
                                    <i class="fas fa-phone-alt"></i> 935 603 163
                                </a>
                                <a href="mailto:geral@angonueve.co" class="quick-action" style="justify-content: center;">
                                    <i class="fas fa-envelope"></i> geral@angonueve.co
                                </a>
                                <a href="https://wa.me/244935603163" target="_blank" class="quick-action" style="justify-content: center;">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
<?php include __DIR__ . '/../includes/spinner.php'; ?>
<script>
const NOTIF_API = '../api/notifications.php';
async function refreshNotifs() {
    try {
        const res = await fetch(NOTIF_API);
        const data = await res.json();
        if (!data.notifications) return;
        const total = document.getElementById('notifTotal');
        if (total) total.textContent = data.total;
        data.notifications.forEach(n => {
            const id = 'notif' + n.type.charAt(0).toUpperCase() + n.type.slice(1);
            const el = document.getElementById(id);
            if (el) el.textContent = n.count;
        });
        const grid = document.getElementById('notifGrid');
        if (grid) {
            grid.querySelectorAll('.notif-card').forEach(c => {
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
