<?php
declare(strict_types=1);


ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Autoloader.php';
Autoloader::register();
require_once __DIR__ . '/core/Helpers.php';
require_once __DIR__ . '/core/Router.php';

if (session_status() === PHP_SESSION_NONE) session_start();


if (empty($_GET['c'])) {
    $_GET['c'] = empty($_SESSION['user']) ? 'auth' : 'pedido';
}
if (empty($_GET['a'])) {
    $_GET['a'] = empty($_SESSION['user']) ? 'login' : 'index';
}


Router::dispatch();
