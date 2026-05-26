<?php
require_once __DIR__ . '/../includes/config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $check = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'admin@angonueve.co'")->fetchColumn();
    if ($check == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'admin', 'active')")
            ->execute(['Administrador', 'admin@angonueve.co', $hash]);
        echo "Admin criado: admin@angonueve.co / admin123\n";
    } else {
        echo "Admin já existe\n";
    }

    $check2 = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'cliente@teste.co'")->fetchColumn();
    if ($check2 == 0) {
        $hash = password_hash('cliente123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'client', 'active')")
            ->execute(['Cliente Teste', 'cliente@teste.co', $hash]);
        echo "Cliente criado: cliente@teste.co / cliente123\n";
    } else {
        echo "Cliente de teste já existe\n";
    }

    $tables = ['invoices', 'payments', 'client_services', 'login_attempts'];
    foreach ($tables as $table) {
        $exists = $pdo->query("SHOW TABLES LIKE '$table'")->fetchColumn();
        echo "Tabela '$table': " . ($exists ? "OK\n" : "FALTOU\n");
    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
