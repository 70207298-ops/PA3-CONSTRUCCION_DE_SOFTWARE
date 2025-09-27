<?php

declare(strict_types=1);

class Router
{
    public static function dispatch(): void
    {
        $controller = trim($_GET['c'] ?? 'pedido');
        $action     = trim($_GET['a'] ?? 'index');

        $controller = preg_replace('/[^a-zA-Z0-9_]/', '', $controller);
        $action     = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

        $class = ucfirst($controller) . 'Controller';
        $file  = __DIR__ . '/../controllers/' . $class . '.php';

        if (!file_exists($file)) {
            http_response_code(404);
            echo "<h2>Controlador no encontrado: {$class}</h2>";
            return;
        }
        require_once $file;
        if (!class_exists($class)) {
            http_response_code(500);
            echo "<h2>Clase de controlador no declarada: {$class}</h2>";
            return;
        }

        $obj = new $class();
        if (!method_exists($obj, $action)) {
            http_response_code(404);
            echo "<h2>Acción no encontrada: {$action}</h2>";
            return;
        }

        call_user_func([$obj, $action]);
    }
}
