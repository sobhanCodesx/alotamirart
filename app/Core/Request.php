<?php
namespace App\Core;

class Request
{
    private $method;
    private $path;
    private $query;
    private $post;
    private $files;
    private $server;

    public function __construct($method, $path, array $query, array $post, array $files, array $server)
    {
        $this->method = strtoupper($method);
        $this->path = $this->normalizePath($path);
        $this->query = $query;
        $this->post = $post;
        $this->files = $files;
        $this->server = $server;
    }

    public static function capture()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $path = parse_url($uri, PHP_URL_PATH);
        return new self(
            isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET',
            $path ?: '/',
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER
        );
    }

    public function method() { return $this->method; }
    public function path() { return $this->path; }

    public function query($key = null, $default = null)
    {
        if ($key === null) return $this->query;
        return array_key_exists($key, $this->query) ? $this->query[$key] : $default;
    }

    public function input($key = null, $default = null)
    {
        if ($key === null) return $this->post;
        return array_key_exists($key, $this->post) ? $this->post[$key] : $default;
    }

    public function file($key)
    {
        return array_key_exists($key, $this->files) ? $this->files[$key] : null;
    }

    public function allWithFiles()
    {
        return array_merge($this->post, $this->files);
    }

    public function server($key, $default = null)
    {
        return array_key_exists($key, $this->server) ? $this->server[$key] : $default;
    }

    private function normalizePath($path)
    {
        $decoded = rawurldecode((string) $path);
        $trimmed = trim($decoded, '/');
        return $trimmed === '' ? '/' : $trimmed;
    }
}
