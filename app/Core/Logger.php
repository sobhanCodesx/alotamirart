<?php
namespace App\Core;

class Logger
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
        $dir = dirname($file);
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
    }

    public function error($message, array $context = [])
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ERROR ' . $message;
        if (!empty($context)) $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $line .= PHP_EOL;
        @file_put_contents($this->file, $line, FILE_APPEND | LOCK_EX);
    }

    public function exception(\Throwable $e)
    {
        $this->error($e->getMessage(), [
            'type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
