<?php
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitizeArray($data) {
    return array_map('sanitize', $data);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^\+?[0-9\s\-]{7,15}$/', $phone);
}

function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? '';
}

function getBrowser($ua) {
    $browsers = ['Edg' => 'Edge', 'OPR' => 'Opera', 'Chrome' => 'Chrome', 'Firefox' => 'Firefox', 'Safari' => 'Safari'];
    foreach ($browsers as $key => $name) {
        if (strpos($ua, $key) !== false) return $name;
    }
    return 'Unknown';
}

function getOS($ua) {
    $oss = ['Windows NT' => 'Windows', 'Mac OS' => 'macOS', 'Linux' => 'Linux', 'Android' => 'Android', 'iPhone' => 'iOS'];
    foreach ($oss as $key => $name) {
        if (strpos($ua, $key) !== false) return $name;
    }
    return 'Unknown';
}

function getDevice($ua) {
    if (preg_match('/Mobile|Android|iPhone|iPad/i', $ua)) return 'Mobile';
    if (preg_match('/Tablet|iPad/i', $ua)) return 'Tablet';
    return 'Desktop';
}

function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    $intervals = [
        31536000 => 'ano', 2592000 => 'mês', 604800 => 'semana',
        86400 => 'dia', 3600 => 'hora', 60 => 'minuto', 1 => 'segundo'
    ];
    foreach ($intervals as $secs => $label) {
        $count = floor($diff / $secs);
        if ($count >= 1) {
            $plural = $count > 1 && $label !== 'mês' ? $label . 's' : $label;
            return "há {$count} {$plural}";
        }
    }
    return 'agora mesmo';
}

function statusBadge($status) {
    $colors = [
        'unread' => 'danger', 'read' => 'warning', 'replied' => 'success', 'archived' => 'secondary',
        'pending' => 'warning', 'confirmed' => 'info', 'in_progress' => 'primary', 'completed' => 'success', 'cancelled' => 'danger',
        'paid' => 'success', 'refunded' => 'info',
        'active' => 'success', 'suspended' => 'danger', 'expired' => 'secondary',
        'rejected' => 'danger'
    ];
    $color = $colors[$status] ?? 'secondary';
    $labels = [
        'in_progress' => 'Em Progresso', 'pending' => 'Pendente', 'confirmed' => 'Confirmado',
        'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'paid' => 'Pago',
        'refunded' => 'Reembolsado', 'active' => 'Activo', 'suspended' => 'Suspenso',
        'expired' => 'Expirado', 'rejected' => 'Rejeitado', 'unread' => 'Não Lida',
        'read' => 'Lida', 'replied' => 'Respondida', 'archived' => 'Arquivada'
    ];
    $label = $labels[$status] ?? ucfirst($status);
    return "<span class=\"badge badge-{$color}\">{$label}</span>";
}

function logActivity($userId, $action, $description = '') {
    try {
        db()->insert('activity_log', [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => getIP()
        ]);
    } catch (Exception $e) {}
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function getSetting($key, $default = '') {
    try {
        $row = db()->fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

function updateSetting($key, $value) {
    try {
        $exists = db()->count('settings', 'setting_key = ?', [$key]);
        if ($exists) {
            db()->update('settings', ['setting_value' => $value], 'setting_key = :key', ['key' => $key]);
        } else {
            db()->insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getDashboardStats() {
    return [
        'total_visitors' => db()->count('visitors'),
        'today_visitors' => db()->count('visitors', 'DATE(visited_at) = CURDATE()'),
        'total_messages' => db()->count('messages'),
        'unread_messages' => db()->count('messages', "status = 'unread'"),
        'total_orders' => db()->count('orders'),
        'pending_orders' => db()->count('orders', "status = 'pending'"),
        'total_services' => count(getServiceList()),
        'total_invoices' => db()->count('invoices'),
        'pending_invoices' => db()->count('invoices', "status = 'pending'"),
        'total_payments' => db()->count('payments', "status = 'confirmed'"),
        'pending_payments' => db()->count('payments', "status = 'pending'"),
    ];
}

function getServiceList() {
    return [
        'hospedagem' => 'Hospedagem de Sites',
        'dominios' => 'Registo de Domínios',
        'email' => 'Email Corporativo',
        'criacao-sites' => 'Criação de Sites Profissionais'
    ];
}

function generateInvoiceNo() {
    $prefix = getSetting('invoice_prefix', 'INV-');
    $nextNum = intval(getSetting('invoice_next_number', '1001'));
    $invoiceNo = $prefix . $nextNum;
    updateSetting('invoice_next_number', $nextNum + 1);
    return $invoiceNo;
}

function getClientInvoices($clientId) {
    return db()->fetchAll("SELECT * FROM invoices WHERE client_id = ? ORDER BY created_at DESC", [$clientId]);
}

function getClientPayments($clientId) {
    return db()->fetchAll("SELECT * FROM payments WHERE client_id = ? ORDER BY created_at DESC", [$clientId]);
}

function getClientServices($clientId) {
    return db()->fetchAll("SELECT * FROM client_services WHERE client_id = ? ORDER BY created_at DESC", [$clientId]);
}

function activateClientService($clientId, $serviceSlug, $serviceName, $planName, $invoiceId) {
    $existing = db()->fetchOne(
        "SELECT id FROM client_services WHERE client_id = ? AND service_slug = ? AND status = 'active'",
        [$clientId, $serviceSlug]
    );
    if ($existing) return $existing['id'];

    $expiresAt = null;
    if ($planName && strpos(strtolower($planName), 'anual') !== false) {
        $expiresAt = date('Y-m-d', strtotime('+1 year'));
    } elseif ($planName && strpos(strtolower($planName), 'mensal') !== false) {
        $expiresAt = date('Y-m-d', strtotime('+1 month'));
    }

    return db()->insert('client_services', [
        'client_id' => $clientId,
        'invoice_id' => $invoiceId,
        'service_slug' => $serviceSlug,
        'service_name' => $serviceName,
        'plan_name' => $planName,
        'status' => 'active',
        'activated_at' => date('Y-m-d H:i:s'),
        'expires_at' => $expiresAt
    ]);
}

function validateNIF($nif) {
    $nif = preg_replace('/[^0-9]/', '', $nif);
    if (strlen($nif) !== 10) return false;

    $digits = str_split($nif);
    $sum = 0;
    $weights = [9, 8, 7, 6, 5, 4, 3, 2, 1];
    for ($i = 0; $i < 9; $i++) {
        $sum += intval($digits[$i]) * $weights[$i];
    }
    $checkDigit = $sum % 11;
    if ($checkDigit === 10) $checkDigit = 0;
    return intval($digits[9]) === $checkDigit;
}

function formatNIF($nif) {
    $nif = preg_replace('/[^0-9]/', '', $nif);
    if (strlen($nif) === 10) {
        return substr($nif, 0, 7) . '-' . substr($nif, 7, 3);
    }
    return $nif;
}

function getRevenueStats($startDate = null, $endDate = null) {
    $where = "WHERE i.status = 'paid'";
    $params = [];
    if ($startDate) { $where .= ' AND i.paid_at >= ?'; $params[] = $startDate; }
    if ($endDate) { $where .= ' AND i.paid_at <= ?'; $params[] = $endDate . ' 23:59:59'; }

    $totalRevenue = db()->fetchOne(
        "SELECT COALESCE(SUM(i.total), 0) as total FROM invoices i {$where}",
        $params
    )['total'];

    $totalPaidWhere = "status = 'paid'";
    $totalPaidParams = [];
    if ($startDate) { $totalPaidWhere .= ' AND paid_at >= ?'; $totalPaidParams[] = $startDate; }
    if ($endDate) { $totalPaidWhere .= ' AND paid_at <= ?'; $totalPaidParams[] = $endDate . ' 23:59:59'; }
    $totalPaid = db()->count('invoices', $totalPaidWhere, $totalPaidParams);

    $monthlyRevenue = db()->fetchAll(
        "SELECT DATE_FORMAT(i.paid_at, '%Y-%m') as month, COALESCE(SUM(i.total), 0) as total, COUNT(*) as count
         FROM invoices i WHERE i.status = 'paid'
         GROUP BY DATE_FORMAT(i.paid_at, '%Y-%m') ORDER BY month DESC LIMIT 12"
    );

    $revenueByService = db()->fetchAll(
        "SELECT COALESCE(i.service_name, 'Outro') as service, COALESCE(SUM(i.total), 0) as total, COUNT(*) as count
         FROM invoices i WHERE i.status = 'paid'
         GROUP BY i.service_name ORDER BY total DESC"
    );

    $revenueByMethod = db()->fetchAll(
        "SELECT COALESCE(i.payment_method, 'N/A') as method, COALESCE(SUM(i.total), 0) as total, COUNT(*) as count
         FROM invoices i WHERE i.status = 'paid' AND i.payment_method IS NOT NULL
         GROUP BY i.payment_method ORDER BY total DESC"
    );

    $topClients = db()->fetchAll(
        "SELECT i.client_id, i.client_name, COALESCE(SUM(i.total), 0) as total, COUNT(*) as count
         FROM invoices i WHERE i.status = 'paid'
         GROUP BY i.client_id, i.client_name ORDER BY total DESC LIMIT 10"
    );

    $pendingTotal = db()->fetchOne(
        "SELECT COALESCE(SUM(total), 0) as total FROM invoices WHERE status = 'pending'"
    )['total'];

    return [
        'total_revenue' => $totalRevenue,
        'total_paid_invoices' => $totalPaid,
        'pending_total' => $pendingTotal,
        'monthly' => $monthlyRevenue,
        'by_service' => $revenueByService,
        'by_method' => $revenueByMethod,
        'top_clients' => $topClients,
    ];
}

function getAbandonedOrders($daysThreshold = 2) {
    $abandoned = db()->fetchAll(
        "SELECT o.*, 
         DATEDIFF(NOW(), o.created_at) as days_old
         FROM orders o 
         WHERE o.status = 'pending' 
         AND o.created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
         AND o.id NOT IN (SELECT order_id FROM invoices WHERE order_id IS NOT NULL)
         ORDER BY o.created_at DESC",
        [$daysThreshold]
    );

    $cancelled = db()->fetchAll(
        "SELECT o.*, DATEDIFF(NOW(), o.created_at) as days_old
         FROM orders o WHERE o.status = 'cancelled' ORDER BY o.created_at DESC LIMIT 50"
    );

    $totalAbandoned = count($abandoned);
    $totalCancelled = count($cancelled);
    $abandonedValue = array_sum(array_column($abandoned, 'price_monthly'));
    $cancelledValue = array_sum(array_column($cancelled, 'price_monthly'));

    return [
        'abandoned' => $abandoned,
        'cancelled' => $cancelled,
        'total_abandoned' => $totalAbandoned,
        'total_cancelled' => $totalCancelled,
        'abandoned_value' => $abandonedValue,
        'cancelled_value' => $cancelledValue,
    ];
}

function exportCSV($headers, $rows, $filename = 'export.csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($output, $headers, ';');
    foreach ($rows as $row) {
        fputcsv($output, $row, ';');
    }
    fclose($output);
    exit;
}

function uploadProofFile($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Erro no upload do ficheiro'];
    }
    $maxSize = MAX_UPLOAD_SIZE;
    if ($file['size'] > $maxSize) {
        return ['error' => 'Ficheiro muito grande. Máximo 10MB'];
    }
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Formato não permitido. Use JPG, PNG, GIF, WebP ou PDF'];
    }
    $uploadDir = UPLOAD_DIR;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'proof_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $destPath = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['error' => 'Erro ao guardar o ficheiro'];
    }
    return ['filename' => $filename, 'original' => $file['name']];
}

function uploadChatFile($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Erro no upload do ficheiro'];
    }
    $maxSize = MAX_UPLOAD_SIZE;
    if ($file['size'] > $maxSize) {
        return ['error' => 'Ficheiro muito grande. Máximo 10MB'];
    }
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Formato não permitido. Use JPG, PNG, GIF, WebP ou PDF'];
    }
    $uploadDir = UPLOAD_DIR . '/chat';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'chat_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $destPath = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['error' => 'Erro ao guardar o ficheiro'];
    }
    return ['filename' => $filename, 'original' => $file['name']];
}

function uploadEmployeePhoto($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Erro no upload da foto'];
    }
    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['error' => 'Foto muito grande. Máximo 2MB'];
    }
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Formato não permitido. Use JPG, PNG, GIF ou WebP'];
    }
    $uploadDir = UPLOAD_DIR . '/employees';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'emp_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $destPath = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['error' => 'Erro ao guardar a foto'];
    }
    return ['filename' => $filename, 'original' => $file['name']];
}

function getEmployeePermissions($userId) {
    $rows = db()->fetchAll("SELECT permission FROM permissions WHERE user_id = ?", [$userId]);
    return array_column($rows, 'permission');
}

function setEmployeePermissions($userId, $perms) {
    db()->query("DELETE FROM permissions WHERE user_id = ?", [$userId]);
    foreach ($perms as $p) {
        if (trim($p)) {
            db()->insert('permissions', ['user_id' => $userId, 'permission' => trim($p)]);
        }
    }
}

function sendEmail($to, $subject, $body, $altBody = '') {
    try {
        $driver = getSetting('mail_driver', 'smtp');
        $from = getSetting('mail_from', '');
        $fromName = getSetting('mail_from_name', getSetting('site_name', 'ANGONUEVE'));

        if (!$from) {
            $from = getSetting('site_email', 'noreply@angonueve.co');
        }

        $autoload = __DIR__ . '/../vendor/phpmailer/phpmailer/PHPMailer.php';
        if (file_exists($autoload)) {
            require_once $autoload;
            require_once __DIR__ . '/../vendor/phpmailer/phpmailer/SMTP.php';
            require_once __DIR__ . '/../vendor/phpmailer/phpmailer/Exception.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->CharSet = 'UTF-8';

            if ($driver === 'smtp') {
                $host = getSetting('smtp_host', '');
                if ($host) {
                    $mail->isSMTP();
                    $mail->Host = $host;
                    $mail->Port = intval(getSetting('smtp_port', 587));
                    $user = getSetting('smtp_user', '');
                    if ($user) {
                        $mail->SMTPAuth = true;
                        $mail->Username = $user;
                        $mail->Password = getSetting('smtp_pass', '');
                    }
                    $enc = getSetting('smtp_encryption', 'tls');
                    if ($enc === 'tls') $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    elseif ($enc === 'ssl') $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                }
            }

            $mail->setFrom($from, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            if ($altBody) {
                $mail->AltBody = $altBody;
            } else {
                $mail->AltBody = strip_tags($body);
            }

            $mail->send();
            return true;
        }

        throw new Exception('PHPMailer not found');
    } catch (Exception $e) {
        error_log("sendEmail failed: " . $e->getMessage());
        try {
            $from = getSetting('mail_from', getSetting('site_email', 'noreply@angonueve.co'));
            $fromName = getSetting('mail_from_name', getSetting('site_name', 'ANGONUEVE'));
            $headers = "From: {$fromName} <{$from}>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            if (!mail($to, $subject, $body, $headers)) {
                return false;
            }
            return true;
        } catch (Exception $e2) {
            error_log("sendEmail fallback mail() failed: " . $e2->getMessage());
            return false;
        }
    }
}

function emailTemplateInvoiceCreated($clientName, $invoiceNo, $total, $link) {
    $siteName = getSetting('site_name', 'ANGONUEVE');
    return '
    <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f7fa;">
        <div style="background: #1a3a5c; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="color: #ffffff; margin: 0; font-size: 22px;">' . $siteName . '</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <p style="color: #333; font-size: 16px;">Olá <strong>' . $clientName . '</strong>,</p>
            <p style="color: #555; font-size: 15px;">A sua factura foi criada com sucesso.</p>
            <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
                <tr><td style="padding: 10px; color: #666;">Factura:</td><td style="padding: 10px; font-weight: bold; color: #1a3a5c;">' . $invoiceNo . '</td></tr>
                <tr><td style="padding: 10px; color: #666;">Valor Total:</td><td style="padding: 10px; font-weight: bold; color: #1a3a5c; font-size: 18px;">Kz ' . number_format($total, 0, ',', ' ') . '</td></tr>
            </table>
            <div style="text-align: center; margin: 25px 0;">
                <a href="' . $link . '" style="background: #1a3a5c; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-size: 15px; display: inline-block;">Ver Factura</a>
            </div>
            <p style="color: #999; font-size: 13px; text-align: center;">Obrigado pela sua preferência.</p>
        </div>
    </div>';
}

function emailTemplatePaymentConfirmed($clientName, $invoiceNo, $amount) {
    $siteName = getSetting('site_name', 'ANGONUEVE');
    return '
    <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f7fa;">
        <div style="background: #1a8a3c; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="color: #ffffff; margin: 0; font-size: 22px;">Pagamento Confirmado</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <p style="color: #333; font-size: 16px;">Olá <strong>' . $clientName . '</strong>,</p>
            <p style="color: #555; font-size: 15px;">O pagamento da factura <strong>' . $invoiceNo . '</strong> foi confirmado com sucesso.</p>
            <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
                <tr><td style="padding: 10px; color: #666;">Valor Pago:</td><td style="padding: 10px; font-weight: bold; color: #1a8a3c; font-size: 18px;">Kz ' . number_format($amount, 0, ',', ' ') . '</td></tr>
            </table>
            <p style="color: #555; font-size: 15px;">O seu serviço já se encontra activo. Obrigado pela confiança!</p>
            <p style="color: #999; font-size: 13px; text-align: center;">' . $siteName . '</p>
        </div>
    </div>';
}

function emailTemplatePaymentRejected($clientName, $invoiceNo, $reason) {
    $siteName = getSetting('site_name', 'ANGONUEVE');
    return '
    <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f7fa;">
        <div style="background: #c0392b; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="color: #ffffff; margin: 0; font-size: 22px;">Pagamento Rejeitado</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <p style="color: #333; font-size: 16px;">Olá <strong>' . $clientName . '</strong>,</p>
            <p style="color: #555; font-size: 15px;">O pagamento da factura <strong>' . $invoiceNo . '</strong> foi rejeitado.</p>
            ' . ($reason ? '<p style="color: #c0392b; font-size: 14px; background: #fdf0ef; padding: 12px; border-radius: 6px;"><strong>Motivo:</strong> ' . $reason . '</p>' : '') . '
            <p style="color: #555; font-size: 15px;">Por favor, contacte-nos para regularizar a situação.</p>
            <p style="color: #999; font-size: 13px; text-align: center;">' . $siteName . '</p>
        </div>
    </div>';
}

function emailTemplateNewOrder($orderId, $serviceName, $clientName) {
    $siteName = getSetting('site_name', 'ANGONUEVE');
    return '
    <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f7fa;">
        <div style="background: #2c3e50; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="color: #ffffff; margin: 0; font-size: 22px;">Nova Encomenda #' . $orderId . '</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <p style="color: #333; font-size: 16px;">Nova encomenda registada no ' . $siteName . '.</p>
            <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
                <tr><td style="padding: 10px; color: #666;">Cliente:</td><td style="padding: 10px; font-weight: bold; color: #333;">' . $clientName . '</td></tr>
                <tr><td style="padding: 10px; color: #666;">Serviço:</td><td style="padding: 10px; font-weight: bold; color: #333;">' . $serviceName . '</td></tr>
                <tr><td style="padding: 10px; color: #666;">Encomenda:</td><td style="padding: 10px; font-weight: bold; color: #2c3e50;">#' . $orderId . '</td></tr>
            </table>
        </div>
    </div>';
}

function emailTemplateContractExpiring($clientName, $contractNumber, $daysLeft) {
    $siteName = getSetting('site_name', 'ANGONUEVE');
    return '
    <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f7fa;">
        <div style="background: #e67e22; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="color: #ffffff; margin: 0; font-size: 22px;">Contrato a Expirar</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <p style="color: #333; font-size: 16px;">Olá <strong>' . $clientName . '</strong>,</p>
            <p style="color: #555; font-size: 15px;">O seu contrato <strong>' . $contractNumber . '</strong> irá expirar dentro de <strong>' . $daysLeft . ' dias</strong>.</p>
            <p style="color: #555; font-size: 15px;">Para evitar a interrupção dos serviços, por favor renove o seu contrato.</p>
            <p style="color: #999; font-size: 13px; text-align: center;">' . $siteName . '</p>
        </div>
    </div>';
}

function emailTemplatePayslipGenerated($employeeName, $monthYear, $netSalary) {
    $siteName = getSetting('site_name', 'ANGONUEVE');
    return '
    <div style="font-family: Arial, Helvetica, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f4f7fa;">
        <div style="background: #2980b9; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="color: #ffffff; margin: 0; font-size: 22px;">Recibo de Vencimento</h1>
        </div>
        <div style="background: #ffffff; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <p style="color: #333; font-size: 16px;">Olá <strong>' . $employeeName . '</strong>,</p>
            <p style="color: #555; font-size: 15px;">O seu recibo de vencimento referente a <strong>' . $monthYear . '</strong> já se encontra disponível.</p>
            <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
                <tr><td style="padding: 10px; color: #666;">Valor Líquido:</td><td style="padding: 10px; font-weight: bold; color: #2980b9; font-size: 18px;">Kz ' . number_format($netSalary, 0, ',', ' ') . '</td></tr>
            </table>
            <p style="color: #999; font-size: 13px; text-align: center;">' . $siteName . '</p>
        </div>
    </div>';
}

function getAllPermissions() {
    return [
        'dashboard' => 'Dashboard',
        'messages' => 'Mensagens',
        'orders' => 'Encomendas',
        'invoices' => 'Facturas',
        'payments' => 'Pagamentos',
        'revenue' => 'Faturamento',
        'support_chat' => 'Chat Suporte',
        'abandoned' => 'Compras Abandonadas',
        'visitors' => 'Visitantes',
        'clients' => 'Clientes',
        'employees' => 'Funcionários',
        'chatbot' => 'Chatbot',
        'settings' => 'Configurações',
        'activity_log' => 'Registo de Actividades',
        'payslips' => 'Recibos de Vencimento',
        'contracts' => 'Contratos'
    ];
}
