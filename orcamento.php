<?php
require_once __DIR__ . '/includes/auth.php';

$user = currentUser();
$service = sanitize($_GET['service'] ?? '');
$plan = sanitize($_GET['plan'] ?? '');
$template = sanitize($_GET['template'] ?? '');
$subject = 'Pedido de Orçamento';
if ($service) {
    $subjParts = [ucfirst($service)];
    if ($plan) $subjParts[] = $plan;
    if ($template) $subjParts[] = 'Template: ' . ucfirst(str_replace('-', ' ', $template));
    $subject .= ' - ' . implode(' / ', $subjParts);
}

if ($user && $user['role'] === 'client') {
    $msg = "Olá, gostaria de solicitar um orçamento.";
    if ($service) $msg .= "\n\nServiço: " . ucfirst($service);
    if ($plan) $msg .= "\nPlano: {$plan}";
    if ($template) $msg .= "\nTemplate: " . ucfirst(str_replace('-', ' ', $template));
    $qs = http_build_query(['subject' => $subject, 'message' => $msg]);
    header('Location: client/chat.php?' . $qs);
    exit;
}

$_SESSION['orcamento_intent'] = ['subject' => $subject, 'service' => $service, 'plan' => $plan, 'template' => $template];
header('Location: client/login.php?redirect=' . urlencode('orcamento.php?' . $_SERVER['QUERY_STRING']));
exit;
