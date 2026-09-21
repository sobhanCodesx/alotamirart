<?php

class LegacyRouteMatcher
{
    public static function match($routePath, $requestPath)
    {
        $routePath = trim((string)$routePath, '/ ');
        $requestPath = trim((string)$requestPath, '/ ');

        if ($routePath === '' && $requestPath === '') {
            return [];
        }

        $parts = preg_split(
            '/(\{[A-Za-z_][A-Za-z0-9_]*\})/',
            $routePath,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $regex = '';
        foreach ($parts as $part) {
            if (preg_match('/^\{[A-Za-z_][A-Za-z0-9_]*\}$/', $part)) {
                $regex .= '([^/]+)';
            } else {
                $regex .= preg_quote($part, '#');
            }
        }

        if (!preg_match('#^' . $regex . '$#u', $requestPath, $matches)) {
            return null;
        }

        array_shift($matches);
        return array_map('rawurldecode', $matches);
    }
}
