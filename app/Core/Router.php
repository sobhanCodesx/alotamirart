<?php
namespace App\Core;

class Router
{
    private $container;
    private $request;
    private $routes = [];

    public function __construct(Container $container, Request $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function get($pattern, $handler) { return $this->add('GET', $pattern, $handler); }
    public function post($pattern, $handler) { return $this->add('POST', $pattern, $handler); }

    public function add($method, $pattern, $handler)
    {
        $pattern = $this->normalizePattern($pattern);
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'regex' => $this->compile($pattern),
            'handler' => $handler,
        ];
        return $this;
    }

    public function dispatch()
    {
        $path = $this->request->path();
        $method = $this->request->method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            if (!preg_match($route['regex'], $path, $matches)) continue;

            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) $params[] = rawurldecode($value);
            }
            return $this->invoke($route['handler'], $params);
        }

        http_response_code(404);
        if (is_file(BASE_PATH . '/404.php')) {
            require BASE_PATH . '/404.php';
            return null;
        }
        echo '404';
        return null;
    }

    private function invoke($handler, array $params)
    {
        if (is_callable($handler) && !is_array($handler)) return call_user_func_array($handler, $params);

        if (is_array($handler) && count($handler) === 2) {
            $controller = is_object($handler[0]) ? $handler[0] : $this->container->get($handler[0]);
            return call_user_func_array([$controller, $handler[1]], $params);
        }
        throw new \RuntimeException('Invalid route handler.');
    }

    private function compile($pattern)
    {
        if ($pattern === '/') return '#^/$#u';

        $regex = '';
        $offset = 0;
        if (preg_match_all('/\{([A-Za-z_][A-Za-z0-9_]*)\}/', $pattern, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $index => $match) {
                $token = $match[0];
                $position = $match[1];
                $name = $matches[1][$index][0];
                $regex .= preg_quote(substr($pattern, $offset, $position - $offset), '#');
                $regex .= '(?P<' . $name . '>[^/]+)';
                $offset = $position + strlen($token);
            }
        }
        $regex .= preg_quote(substr($pattern, $offset), '#');
        return '#^' . $regex . '/?$#u';
    }

    private function normalizePattern($pattern)
    {
        $trimmed = trim((string) $pattern, '/');
        return $trimmed === '' ? '/' : $trimmed;
    }
}
