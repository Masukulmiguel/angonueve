<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'angonueve_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_URL', 'http://localhost/ANGONUEVE');
define('SITE_NAME', 'ANGONUEVE');
define('ADMIN_EMAIL', 'geral@angonueve.co');
define('WHATSAPP_NUMBER', '244935603163');

define('SESSION_TIMEOUT', 3600);
define('ITEMS_PER_PAGE', 20);

define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', SITE_URL . '/uploads');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024);

define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900);

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
