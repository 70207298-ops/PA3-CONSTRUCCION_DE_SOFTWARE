<?php

declare(strict_types=1);

class View
{
    public static string $basePath = __DIR__ . '/../views';

    public static function render(string $view, array $data = []): void
    {
        $viewFile = rtrim(self::$basePath, '/\\') . '/' . ltrim($view, '/\\') . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "<h2>Vista no encontrada: {$viewFile}</h2>";
            return;
        }
        extract($data, EXTR_SKIP);
        $layoutHeader = self::$basePath . '/layouts/header.php';
        $layoutMenu   = self::$basePath . '/layouts/menu.php';
        $layoutFooter = self::$basePath . '/layouts/footer.php';

        if (file_exists($layoutHeader)) include $layoutHeader;
        if (file_exists($layoutMenu))   include $layoutMenu;

        include $viewFile;

        if (file_exists($layoutFooter)) include $layoutFooter;
    }

    public static function e(?string $str): string
    {
        return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function asset(string $path): string
    {
        $path = ltrim($path, '/');
        return 'public/' . $path;
    }
}
