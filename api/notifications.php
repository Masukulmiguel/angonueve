<?php
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');

$user = currentUser();
if (!$user) jsonResponse(['error' => 'Não autenticado'], 401);

if ($user['role'] === 'admin') {
    $pendingOrders = db()->count('orders', "status = 'pending'");
    $pendingPayments = db()->count('payments', "status = 'pending'");
    $unreadChat = db()->count('support_chat', "is_read = 0 AND sender_type = 'client'");
    $unreadMessages = db()->count('messages', "status = 'unread'");
    $abandoned = db()->fetchOne("SELECT COUNT(*) as total, COALESCE(SUM(price_monthly), 0) as valor FROM orders WHERE status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)");
    $unreadWa = db()->count('whatsapp_conversations', "unread > 0");

    jsonResponse([
        'role' => 'admin',
        'notifications' => [
            ['type' => 'orders', 'icon' => 'fa-shopping-cart', 'label' => 'Encomendas pendentes', 'count' => $pendingOrders, 'url' => 'orders.php?status=pending', 'color' => 'orange'],
            ['type' => 'payments', 'icon' => 'fa-credit-card', 'label' => 'Pagamentos por confirmar', 'count' => $pendingPayments, 'url' => 'payments.php?status=pending', 'color' => 'red'],
            ['type' => 'chat', 'icon' => 'fa-comments', 'label' => 'Chat mensagens não lidas', 'count' => $unreadChat, 'url' => 'support-chat.php', 'color' => 'blue'],
            ['type' => 'messages', 'icon' => 'fa-envelope', 'label' => 'Contacto mensagens não lidas', 'count' => $unreadMessages, 'url' => 'messages.php', 'color' => 'purple'],
            ['type' => 'abandoned', 'icon' => 'fa-cart-arrow-down', 'label' => 'Compras abandonadas', 'count' => intval($abandoned['total'] ?? 0), 'url' => 'abandoned.php', 'color' => 'pink'],
            ['type' => 'whatsapp', 'icon' => 'fa-whatsapp', 'label' => 'Conversas WhatsApp não lidas', 'count' => $unreadWa, 'url' => 'whatsapp.php', 'color' => 'green']
        ],
        'total' => $pendingOrders + $pendingPayments + $unreadChat + $unreadMessages + intval($abandoned['total'] ?? 0) + $unreadWa
    ]);
} else {
    $email = $user['email'];
    $pendingInvoices = db()->count('invoices', "client_id = ? AND status = 'pending'", [$user['id']]);
    $pendingOrders = db()->count('orders', "customer_email = ? AND status IN ('pending','confirmed')", [$email]);
    $unreadChat = db()->count('support_chat', "client_id = ? AND is_read = 0 AND sender_type = 'admin'", [$user['id']]);

    jsonResponse([
        'role' => 'client',
        'notifications' => [
            ['type' => 'invoices', 'icon' => 'fa-file-invoice', 'label' => 'Facturas por pagar', 'count' => $pendingInvoices, 'url' => 'invoices.php', 'color' => 'red'],
            ['type' => 'orders', 'icon' => 'fa-shopping-cart', 'label' => 'Encomendas pendentes', 'count' => $pendingOrders, 'url' => 'orders.php', 'color' => 'orange'],
            ['type' => 'chat', 'icon' => 'fa-comments', 'label' => 'Mensagens novas no chat', 'count' => $unreadChat, 'url' => 'chat.php', 'color' => 'blue']
        ],
        'total' => $pendingInvoices + $pendingOrders + $unreadChat
    ]);
}
