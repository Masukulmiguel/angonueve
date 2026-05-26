<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $customerName = sanitize($input['customer_name'] ?? '');
    $customerEmail = sanitize($input['customer_email'] ?? '');
    $customerPhone = sanitize($input['customer_phone'] ?? '');
    $serviceId = sanitize($input['service_id'] ?? '');
    $planName = sanitize($input['plan_name'] ?? '');
    $paymentType = sanitize($input['payment_type'] ?? 'monthly');
    $priceMonthly = floatval($input['price_monthly'] ?? 0);
    $priceYearly = $priceMonthly * 12;

    if (empty($customerName) || empty($customerEmail) || empty($serviceId)) {
        jsonResponse(['error' => 'Campos obrigatórios em falta'], 400);
    }

    $services = getServiceList();
    $serviceName = $services[$serviceId] ?? $serviceId;

    try {
        $id = db()->insert('orders', [
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'service_id' => $serviceId,
            'service_name' => $serviceName,
            'plan_name' => $planName,
            'price_monthly' => $priceMonthly,
            'price_yearly' => $priceYearly,
            'payment_type' => $paymentType,
            'status' => 'pending'
        ]);

        $whatsapp = getSetting('whatsapp_number', '244935603163');
        $msg = "Nova encomenda ANGONUEVE!%0A";
        $msg .= "*Cliente:* " . urlencode($customerName) . "%0A";
        $msg .= "*Serviço:* " . urlencode($serviceName) . "%0A";
        $msg .= "*Plano:* " . urlencode($planName) . "%0A";
        $msg .= "*Contacto:* " . urlencode($customerPhone) . "%0A";
        $msg .= "*Email:* " . urlencode($customerEmail);

        try {
            $adminEmail = ADMIN_EMAIL;
            $html = emailTemplateNewOrder($id, $serviceName, $customerName);
            sendEmail($adminEmail, 'Nova Encomenda #' . $id . ' - ' . $serviceName, $html);
        } catch (Exception $e) {
            error_log("Email order notification failed: " . $e->getMessage());
        }

        jsonResponse([
            'success' => true,
            'order_id' => $id,
            'whatsapp_url' => "https://wa.me/{$whatsapp}?text={$msg}"
        ], 201);
    } catch (Exception $e) {
        jsonResponse(['error' => 'Erro ao registar encomenda'], 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once __DIR__ . '/../includes/auth.php';
    requireLogin();

    $status = $_GET['status'] ?? '';
    $page = max(1, intval($_GET['page'] ?? 1));
    $perPage = ITEMS_PER_PAGE;
    $offset = ($page - 1) * $perPage;

    $where = '';
    $params = [];
    if ($status) {
        $where = 'WHERE status = ?';
        $params[] = $status;
    }

    $total = db()->count('orders', $where, $params);
    $orders = db()->fetchAll(
        "SELECT * FROM orders {$where} ORDER BY created_at DESC LIMIT ? OFFSET ?",
        array_merge($params, [$perPage, $offset])
    );

    jsonResponse([
        'orders' => $orders,
        'total' => $total,
        'page' => $page,
        'pages' => ceil($total / $perPage)
    ]);
}
