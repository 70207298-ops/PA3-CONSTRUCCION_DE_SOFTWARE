<?php

declare(strict_types=1);

class Database
{
    private static ?\PDO $conn = null;

    public static function get(): \PDO
    {
        if (self::$conn === null) {
            // Cargar config
            $cfg = __DIR__ . '/../config/config.php';
            if (!file_exists($cfg)) {
                throw new \RuntimeException('No se encontró config/config.php');
            }
            require_once $cfg;

            if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER')) {
                throw new \RuntimeException('Constantes de DB no definidas.');
            }
            $host = DB_HOST;
            $db   = DB_NAME;
            $user = DB_USER;
            $pass = defined('DB_PASS') ? DB_PASS : '';
            $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

            $opts = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}"
            ];

            self::$conn = new \PDO($dsn, $user, $pass, $opts);
        }
        return self::$conn;
    }
}
