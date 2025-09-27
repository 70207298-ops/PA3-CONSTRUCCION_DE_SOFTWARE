<?php

declare(strict_types=1);

require_once __DIR__ . '/View.php';

abstract class Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function model(string $name)
    {
        $file = __DIR__ . '/../models/' . $name . '.php';
        if (!class_exists($name)) {
            if (!file_exists($file)) {
                throw new RuntimeException("Modelo no encontrado: {$file}");
            }
            require_once $file;
        }
        return new $name();
    }

    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    // Flash helpers
    protected function flash(string $key, string $msg): void
    {
        $_SESSION[$key] = $msg;
    }

    public static function consumeFlash(string $key): ?string
    {
        if (!empty($_SESSION[$key])) {
            $msg = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $msg;
        }
        return null;
    }
}
