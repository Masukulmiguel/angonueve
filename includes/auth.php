<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

function login($email, $password) {
    if (isRateLimited()) {
        return 'rate_limited';
    }

    $user = db()->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
    if (!$user || !password_verify($password, $user['password'])) {
        recordLoginAttempt($email);
        return 'invalid';
    }
    if ($user['status'] === 'pending') {
        return 'pending';
    }
    if ($user['status'] === 'blocked') {
        return 'blocked';
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['login_time'] = time();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    clearLoginAttempts();
    logActivity($user['id'], 'login', "{$user['name']} fez login como {$user['role']}");
    return true;
}

function isRateLimited() {
    $ip = getIP();
    $attempts = db()->count('login_attempts', 'ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)', [$ip]);
    return $attempts >= MAX_LOGIN_ATTEMPTS;
}

function recordLoginAttempt($email) {
    try {
        db()->insert('login_attempts', ['ip_address' => getIP(), 'email' => $email]);
    } catch (Exception $e) {}
}

function clearLoginAttempts() {
    try {
        db()->delete('login_attempts', 'ip_address = ?', [getIP()]);
    } catch (Exception $e) {}
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function require_csrf() {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!verify_csrf($token)) {
        logActivity($_SESSION['user_id'] ?? 0, 'csrf_fail', 'Tentativa de CSRF detectada');
        jsonResponse(['error' => 'Token de segurança inválido'], 403);
        exit;
    }
}

function getPendingClients() {
    return db()->fetchAll("SELECT * FROM users WHERE role = 'client' AND status = 'pending' ORDER BY created_at DESC");
}

function getAllClients() {
    return db()->fetchAll("SELECT * FROM users WHERE role = 'client' ORDER BY created_at DESC");
}

function requireAdmin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
    if ($_SESSION['user_role'] === 'client') {
        header('Location: ../client/dashboard.php');
        exit;
    }
}

function hasPermission($perm) {
    if (!isLoggedIn()) return false;
    if ($_SESSION['user_role'] === 'admin') return true;
    if ($_SESSION['user_role'] !== 'employee') return false;
    $count = db()->count('permissions', 'user_id = ? AND permission = ?', [$_SESSION['user_id'], $perm]);
    return $count > 0;
}

function requirePermission($perm) {
    if (!hasPermission($perm)) {
        if ($_SESSION['user_role'] === 'employee') {
            header('Location: employee-dashboard.php?error=permission');
            exit;
        }
        header('Location: dashboard.php');
        exit;
    }
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

function isEmployee() {
    return isLoggedIn() && $_SESSION['user_role'] === 'employee';
}

function isClient() {
    return isLoggedIn() && $_SESSION['user_role'] === 'client';
}

function requireClient() {
    if (!isClient()) {
        if (isLoggedIn()) {
            header('Location: ../admin/dashboard.php');
            exit;
        }
        header('Location: login.php');
        exit;
    }
}

function logout() {
    if (isLoggedIn()) {
        logActivity($_SESSION['user_id'], 'logout', 'Usuário fez logout');
    }
    session_destroy();
    header('Location: index.php');
    exit;
}

function checkSessionTimeout() {
    if (isLoggedIn() && (time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
        logout();
    }
    if (isLoggedIn()) {
        $_SESSION['login_time'] = time();
    }
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}
