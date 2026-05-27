<?php
// ============================================================
// ANGONUEVE - Configuration
// Suporta ambiente local (XAMPP) e produção (hospedagem real)
// ============================================================

// --- Database ---
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'angonueve_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// --- Site URL (auto-detect) ---
$detectedUrl = 'http://localhost';
if (!empty($_SERVER['HTTP_HOST'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
    $incDir = str_replace('\\', '/', __DIR__);
    $projectDir = '';
    if ($docRoot && str_starts_with($incDir, $docRoot)) {
        $projectDir = substr(dirname($incDir), strlen($docRoot));
    }
    $detectedUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$projectDir}";
}
define('SITE_URL', getenv('SITE_URL') ?: $detectedUrl);
define('SITE_NAME', getenv('SITE_NAME') ?: 'ANGONUEVE');
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'geral@angonueve.co');
define('WHATSAPP_NUMBER', getenv('WHATSAPP_NUMBER') ?: '244935603163');

// --- Session & Pagination ---
define('SESSION_TIMEOUT', 3600);
define('ITEMS_PER_PAGE', 20);

// --- Uploads ---
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', SITE_URL . '/uploads');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024);

// --- Security ---
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900);

// --- Environment ---
define('ENV', getenv('APP_ENV') ?: 'production');

// --- Error Reporting ---
if (ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}
