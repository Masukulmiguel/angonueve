<?php
require_once __DIR__ . '/includes/auth.php';

$user = currentUser();
$service = sanitize($_GET['service'] ?? '');
$plan = sanitize($_GET['plan'] ?? '');
$subject = $service ? "Pedido de Orçamento - " . ucfirst($service) . ($plan ? " ({$plan})" : '') : 'Pedido de Orçamento';

if ($user && $user['role'] === 'client') {
    $msg = "Olá, gostaria de solicitar um orçamento.";
    if ($service) $msg .= "\n\nServiço: " . ucfirst($service);
    if ($plan) $msg .= "\nPlano: {$plan}";
    $qs = http_build_query(['subject' => $subject, 'message' => $msg]);
    header('Location: client/chat.php?' . $qs);
    exit;
}

$_SESSION['orcamento_intent'] = ['subject' => $subject, 'service' => $service, 'plan' => $plan];
header('Location: client/login.php?redirect=' . urlencode('orcamento.php?' . $_SERVER['QUERY_STRING']));
exit;
