<?php

declare(strict_types=1);

namespace App\Config;

class Env
{
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("File .env tidak ditemukan: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Lewati komentar dan baris tanpa tanda "="
            if (str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim(trim($value), "\"'");

            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? getenv($key);

        return ($value === false || $value === null) ? $default : $value;
    }
}