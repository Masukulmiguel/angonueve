<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

session_start();

$isAdmin = false;
$isLoggedIn = false;
$userName = '';
$userRole = '';

if (isset($_SESSION['user_id'])) {
    $isLoggedIn = true;
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT name, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $userName = $user['name'];
            $userRole = $user['role'];
            $isAdmin = ($user['role'] === 'admin');
        }
    } catch (Exception $e) {
        // return false on error
    }
}

echo json_encode([
    'isAdmin' => $isAdmin,
    'isLoggedIn' => $isLoggedIn,
    'userName' => $userName,
    'userRole' => $userRole
]);
