<?php

declare(strict_types=1);

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(function(string $class){
            $paths = [
                __DIR__ . '/' . $class . '.php',
                __DIR__ . '/../models/' . $class . '.php',
                __DIR__ . '/../controllers/' . $class . '.php',
            ];
            foreach ($paths as $p) {
                if (file_exists($p)) {
                    require_once $p;
                    return;
                }
            }
        });
    }
}
