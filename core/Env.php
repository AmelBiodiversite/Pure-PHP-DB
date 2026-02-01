<?php
namespace Core;

class Env {
    private static $loaded = false;
    private static $vars = [];

    public static function load($path = __DIR__ . '/../.env') {
        if (self::$loaded) {
            return;
        }

        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parser KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                self::$vars[$key] = $value;
                
                // Mettre aussi dans $_ENV pour compatibilité
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null) {
        self::load();
        return self::$vars[$key] ?? getenv($key) ?: $default;
    }
}
