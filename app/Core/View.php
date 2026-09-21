<?php
namespace App\Core;

class View
{
    private $basePath;

    public function __construct($basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
    }

    public function render($path, array $data = [])
    {
        $file = $this->basePath . '/' . ltrim($path, '/');
        if (!is_file($file)) throw new \RuntimeException('View not found: ' . $path);
        extract($data, EXTR_SKIP);
        require $file;
    }
}
