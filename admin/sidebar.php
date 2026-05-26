<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$user = currentUser();
$isEmp = $user && $user['role'] === 'employee';
?>
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-cube"></i>
        <span><?= $isEmp ? 'Funcionário' : 'ANGONUEVE CRM' ?></span>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= $isEmp ? 'employee-dashboard.php' : 'dashboard.php' ?>" class="<?= $currentPage === 'dashboard.php' || $currentPage === 'employee-dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Dashboard
            <?php if (!$isEmp): ?>
            <?php
            $dashNotif = db()->count('orders', "status = 'pending'")
                + db()->count('payments', "status = 'pending'")
                + db()->count('support_chat', "is_read = 0 AND sender_type = 'client'")
                + db()->count('messages', "status = 'unread'");
            ?>
            <?php if ($dashNotif > 0): ?>
                <span class="badge badge-danger" style="margin-left:auto;"><?= $dashNotif ?></span>
            <?php endif; ?>
            <?php endif; ?>
        </a>
        <?php if ($isEmp): ?>
            <?php if (hasPermission('messages')): ?>
            <a href="messages.php" class="<?= $currentPage === 'messages.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Mensagens</a>
            <?php endif; ?>
            <?php if (hasPermission('orders')): ?>
            <a href="orders.php" class="<?= $currentPage === 'orders.php' ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Encomendas</a>
            <?php endif; ?>
            <?php if (hasPermission('invoices')): ?>
            <a href="invoices.php" class="<?= strpos($currentPage, 'invoice') !== false ? 'active' : '' ?>"><i class="fas fa-file-invoice"></i> Facturas</a>
            <?php endif; ?>
            <?php if (hasPermission('payments')): ?>
            <a href="payments.php" class="<?= $currentPage === 'payments.php' ? 'active' : '' ?>"><i class="fas fa-credit-card"></i> Pagamentos</a>
            <?php endif; ?>
            <?php if (hasPermission('revenue')): ?>
            <a href="revenue.php" class="<?= $currentPage === 'revenue.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Faturamento</a>
            <?php endif; ?>
            <?php if (hasPermission('support_chat')): ?>
            <a href="support-chat.php" class="<?= $currentPage === 'support-chat.php' ? 'active' : '' ?>"><i class="fas fa-comments"></i> Chat Suporte</a>
            <?php endif; ?>
            <?php if (hasPermission('abandoned')): ?>
            <a href="abandoned.php" class="<?= $currentPage === 'abandoned.php' ? 'active' : '' ?>"><i class="fas fa-cart-arrow-down"></i> Abandonadas</a>
            <?php endif; ?>
            <?php if (hasPermission('visitors')): ?>
            <a href="visitors.php" class="<?= $currentPage === 'visitors.php' ? 'active' : '' ?>"><i class="fas fa-eye"></i> Visitantes</a>
            <?php endif; ?>
            <?php if (hasPermission('clients')): ?>
            <a href="clients.php" class="<?= $currentPage === 'clients.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Clientes</a>
            <?php endif; ?>
            <?php if (hasPermission('activity_log')): ?>
            <a href="activity-log.php" class="<?= $currentPage === 'activity-log.php' ? 'active' : '' ?>"><i class="fas fa-history"></i> Actividades</a>
            <?php endif; ?>
            <?php if (hasPermission('payslips')): ?>
            <a href="payslips.php" class="<?= $currentPage === 'payslips.php' ? 'active' : '' ?>"><i class="fas fa-file-invoice-dollar"></i> Recibos</a>
            <?php endif; ?>
            <?php if (hasPermission('contracts')): ?>
            <a href="contracts.php" class="<?= $currentPage === 'contracts.php' ? 'active' : '' ?>"><i class="fas fa-file-signature"></i> Contratos</a>
            <?php endif; ?>
        <?php else: ?>
        <a href="messages.php" class="<?= $currentPage === 'messages.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Mensagens</a>
        <a href="orders.php" class="<?= $currentPage === 'orders.php' ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Encomendas</a>
        <a href="invoices.php" class="<?= strpos($currentPage, 'invoice') !== false ? 'active' : '' ?>"><i class="fas fa-file-invoice"></i> Facturas</a>
        <a href="payments.php" class="<?= $currentPage === 'payments.php' ? 'active' : '' ?>"><i class="fas fa-credit-card"></i> Pagamentos
            <?php $pendingPayments = db()->count('payments', "status = 'pending'"); ?>
            <?php if ($pendingPayments > 0): ?>
                <span class="badge badge-danger" style="margin-left:auto;"><?= $pendingPayments ?></span>
            <?php endif; ?>
        </a>
        <a href="revenue.php" class="<?= $currentPage === 'revenue.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Faturamento</a>
        <a href="support-chat.php" class="<?= $currentPage === 'support-chat.php' ? 'active' : '' ?>"><i class="fas fa-comments"></i> Chat Suporte
            <?php $unreadChat = db()->count('support_chat', "is_read = 0 AND sender_type = 'client'"); ?>
            <?php if ($unreadChat > 0): ?>
                <span class="badge badge-danger" style="margin-left:auto;"><?= $unreadChat ?></span>
            <?php endif; ?>
        </a>
        <a href="abandoned.php" class="<?= $currentPage === 'abandoned.php' ? 'active' : '' ?>"><i class="fas fa-cart-arrow-down"></i> Abandonadas
            <?php $abandoned = db()->count('orders', "status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 2 DAY) AND id NOT IN (SELECT order_id FROM invoices WHERE order_id IS NOT NULL)"); ?>
            <?php if ($abandoned > 0): ?>
                <span class="badge badge-danger" style="margin-left:auto;"><?= $abandoned ?></span>
            <?php endif; ?>
        </a>
        <a href="visitors.php" class="<?= $currentPage === 'visitors.php' ? 'active' : '' ?>"><i class="fas fa-eye"></i> Visitantes</a>
        <a href="clients.php" class="<?= $currentPage === 'clients.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Clientes</a>
        <a href="employees.php" class="<?= $currentPage === 'employees.php' ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> Funcionários
            <?php $empCount = db()->count('users', "role = 'employee'"); ?>
            <?php if ($empCount > 0): ?>
                <span class="badge" style="margin-left:auto;background:rgba(255,255,255,0.08);color:var(--text-muted);"><?= $empCount ?></span>
            <?php endif; ?>
        </a>
        <a href="chat-conversations.php" class="<?= $currentPage === 'chat-conversations.php' ? 'active' : '' ?>"><i class="fas fa-robot"></i> Chatbot</a>
        <a href="settings.php" class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Configurações</a>
        <a href="page-backgrounds.php" class="<?= $currentPage === 'page-backgrounds.php' ? 'active' : '' ?>"><i class="fas fa-image"></i> Fundos de Página</a>
        <a href="activity-log.php" class="<?= $currentPage === 'activity-log.php' ? 'active' : '' ?>"><i class="fas fa-history"></i> Actividades</a>
        <a href="payslips.php" class="<?= $currentPage === 'payslips.php' ? 'active' : '' ?>"><i class="fas fa-file-invoice-dollar"></i> Recibos</a>
        <a href="contracts.php" class="<?= $currentPage === 'contracts.php' ? 'active' : '' ?>"><i class="fas fa-file-signature"></i> Contratos</a>
        <?php endif; ?>
        <hr>
        <a href="../index.html" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Site</a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </nav>
</aside>
<?php include __DIR__ . '/../includes/spinner.php'; ?>