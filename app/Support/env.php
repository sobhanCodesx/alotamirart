<?php
/**
 * Minimal .env loader for shared cPanel hosting (PHP 8.1+).
 * No Composer, command line, or third-party dependencies.
 * Server-provided environment variables always take precedence.
 */
declare(strict_types=1);

function aloLoadEnv(?string $path = null): void
{
    static $loaded = [];
    $path ??= dirname(__DIR__, 2) . '/.env';

    if (isset($loaded[$path])) return;
    if (!file_exists($path)) {
        $loaded[$path] = true;
        return;
    }
    if (!is_file($path) || !is_readable($path)) {
        throw new RuntimeException('The .env file is not readable.');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) throw new RuntimeException('Unable to load .env.');

    foreach ($lines as $number => $line) {
        if ($number === 0) $line = preg_replace('/^\xEF\xBB\xBF/', '', $line);
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (str_starts_with($line, 'export ')) $line = ltrim(substr($line, 7));

        if (!preg_match('/^([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/D', $line, $match)) {
            throw new RuntimeException('Invalid .env syntax at line ' . ($number + 1));
        }

        $key = $match[1];
        $value = trim($match[2]);
        if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
            if (strlen($value) < 2 || substr($value, -1) !== $value[0]) {
                throw new RuntimeException('Invalid .env quoted value at line ' . ($number + 1));
            }
            $value = substr($value, 1, -1);
        } else {
            $value = trim((string) preg_replace('/\s+#.*$/', '', $value));
        }

        // Never overwrite a value already supplied by the hosting environment.
        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }

    $loaded[$path] = true;
}
