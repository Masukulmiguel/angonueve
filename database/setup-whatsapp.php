<?php
/**
 * WhatsApp Cloud API Setup
 * Run once after deploying the WhatsApp feature.
 * Creates tables, inserts default settings, and adds permission for admins.
 */

echo "=== WhatsApp Cloud API Setup ===\n\n";

// 1. Run SQL migration
echo "1. Running SQL migration...\n";
$sql = file_get_contents(__DIR__ . '/schema-update5.sql');
$statements = explode(';', $sql);
$count = 0;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=angonueve_db;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt) {
            $pdo->exec($stmt);
            $count++;
        }
    }
    echo "   ✓ {$count} statements executed successfully.\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Add 'whatsapp' permission for all admin and manager users
echo "\n2. Adding 'whatsapp' permission for existing users...\n";
try {
    $users = $pdo->query("SELECT id, name, role FROM users WHERE role IN ('admin', 'manager', 'employee')")->fetchAll(PDO::FETCH_ASSOC);
    $inserted = 0;
    foreach ($users as $user) {
        $existing = $pdo->prepare("SELECT COUNT(*) FROM permissions WHERE user_id = ? AND permission = 'whatsapp'");
        $existing->execute([$user['id']]);
        if ($existing->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO permissions (user_id, permission) VALUES (?, 'whatsapp')")->execute([$user['id']]);
            echo "   + {$user['name']} ({$user['role']})\n";
            $inserted++;
        }
    }
    if ($inserted === 0) {
        echo "   ✓ All users already have 'whatsapp' permission.\n";
    } else {
        echo "   ✓ Added 'whatsapp' permission for {$inserted} user(s).\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error adding permissions: " . $e->getMessage() . "\n";
}

echo "\n=== Setup completed ===\n";
echo "\nNext steps:\n";
echo "1. Go to Admin > Configurações > WhatsApp and fill in:\n";
echo "   - Token de Acesso Permanente (from Meta Developer)\n";
echo "   - ID do Número de Telefone\n";
echo "   - ID da Conta Business (WABA)\n";
echo "2. Configure the Webhook URL in Meta Developer:\n";
echo "   URL: " . (defined('SITE_URL') ? SITE_URL : 'http://localhost/ANGONUEVE') . "/api/whatsapp-webhook.php\n";
echo "   Verify Token: angonueve_wa_verify\n";
