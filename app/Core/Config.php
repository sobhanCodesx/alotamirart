<?php
namespace App\Core;

class Config
{
    private static $items = [];

    public static function load($name)
    {
        if (!array_key_exists($name, self::$items)) {
            $path = BASE_PATH . '/config/' . $name . '.php';
            if (!is_file($path)) {
                throw new \RuntimeException('Config file not found: ' . $name);
            }
            self::$items[$name] = require $path;
        }
        return self::$items[$name];
    }

    public static function get($key, $default = null)
    {
        $segments = explode('.', $key);
        $value = self::load(array_shift($segments));
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}
