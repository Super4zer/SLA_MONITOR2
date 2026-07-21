<?php

namespace App\Config;

class Env
{
    private static array $variables = [];

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Environment file not found: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '#') || empty($line)) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$name, $value] = explode('=', $line, 2);
                $name  = trim($name);
                $value = trim($value, " \t\n\r\0\x0B\"'");

                self::setVar($name, $value);
            }
        }

        self::ensureDbAliases();
    }

    private static function ensureDbAliases(): void
    {
        // DB_USERNAME <-> DB_USER
        if (!empty(self::$variables['DB_USERNAME']) && empty(self::$variables['DB_USER'])) {
            self::setVar('DB_USER', self::$variables['DB_USERNAME']);
        } elseif (!empty(self::$variables['DB_USER']) && empty(self::$variables['DB_USERNAME'])) {
            self::setVar('DB_USERNAME', self::$variables['DB_USER']);
        }

        // DB_PASSWORD <-> DB_PASS
        if (isset(self::$variables['DB_PASSWORD']) && !isset(self::$variables['DB_PASS'])) {
            self::setVar('DB_PASS', self::$variables['DB_PASSWORD']);
        } elseif (isset(self::$variables['DB_PASS']) && !isset(self::$variables['DB_PASSWORD'])) {
            self::setVar('DB_PASSWORD', self::$variables['DB_PASS']);
        }
    }

    private static function setVar(string $name, string $value): void
    {
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        self::$variables[$name] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $val = self::$variables[$key] ?? $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($val !== false && $val !== null && $val !== '') {
            return $val;
        }
        return $default;
    }
}
