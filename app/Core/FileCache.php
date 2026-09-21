<?php
namespace App\Core;

class FileCache
{
    private $path;
    private $enabled;

    public function __construct($path, $enabled = true)
    {
        $this->path = rtrim($path, '/\\');
        $this->enabled = (bool) $enabled;
        if ($this->enabled && !is_dir($this->path)) @mkdir($this->path, 0775, true);
        if ($this->enabled && (!is_dir($this->path) || !is_writable($this->path))) $this->enabled = false;
    }

    public function remember($key, $ttl, callable $callback)
    {
        if (!$this->enabled) return call_user_func($callback);
        $file = $this->file($key);

        if (is_file($file) && (filemtime($file) + (int) $ttl) >= time()) {
            $payload = @file_get_contents($file);
            if ($payload !== false) {
                $value = @unserialize($payload);
                if ($value !== false || $payload === serialize(false)) return $value;
            }
        }

        $value = call_user_func($callback);
        $this->put($key, $value);
        return $value;
    }

    public function put($key, $value)
    {
        if (!$this->enabled) return;
        $file = $this->file($key);
        $tmp = $file . '.' . uniqid('', true) . '.tmp';
        if (@file_put_contents($tmp, serialize($value), LOCK_EX) !== false) @rename($tmp, $file);
        else @unlink($tmp);
    }

    public function forget($key)
    {
        $file = $this->file($key);
        if (is_file($file)) @unlink($file);
    }

    public function clear()
    {
        if (!$this->enabled || !is_dir($this->path)) return;
        foreach (glob($this->path . '/*.cache') ?: [] as $file) @unlink($file);
    }

    private function file($key)
    {
        return $this->path . '/' . sha1($key) . '.cache';
    }
}
