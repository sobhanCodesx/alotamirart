<?php
namespace App\Core;

class Application
{
    private $container;
    private $router;
    private $logger;
    private $debug;

    public function __construct(Container $container, Router $router, Logger $logger, $debug = false)
    {
        $this->container = $container;
        $this->router = $router;
        $this->logger = $logger;
        $this->debug = (bool) $debug;
    }

    public function container() { return $this->container; }
    public function router() { return $this->router; }

    public function run()
    {
        try {
            return $this->router->dispatch();
        } catch (\Throwable $e) {
            $this->logger->exception($e);
            http_response_code(500);
            if ($this->debug) echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</pre>';
            else echo 'خطایی در پردازش درخواست رخ داده است.';
            return null;
        }
    }
}
