<?php
declare(strict_types=1);

session_name('slahpc_session');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'slah_pc';
const DB_USER = 'root';
const DB_PASS = '';

require_once __DIR__ . '/translations.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/app.php';
