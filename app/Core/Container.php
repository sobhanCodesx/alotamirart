<?php
namespace App\Core;

class Container
{
    private $factories = [];
    private $instances = [];

    public function singleton($id, callable $factory)
    {
        $this->factories[$id] = $factory;
        return $this;
    }

    public function instance($id, $instance)
    {
        $this->instances[$id] = $instance;
        return $this;
    }

    public function has($id)
    {
        return array_key_exists($id, $this->instances) || array_key_exists($id, $this->factories);
    }

    public function get($id)
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }
        if (!array_key_exists($id, $this->factories)) {
            throw new \RuntimeException('Service is not registered: ' . $id);
        }
        $this->instances[$id] = call_user_func($this->factories[$id], $this);
        return $this->instances[$id];
    }
}
