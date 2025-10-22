<?php

namespace CrudeSSG;

class Router
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler)
    {
        $fullPattern = $pattern;
        $route = new Route($method, $fullPattern, $handler);
        $this->routes[] = $route;
        return $route;
    }

    public function get(string $pattern, callable $handler)
    {
        return $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler)
    {
        return $this->add('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler)
    {
        return $this->add('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler)
    {
        return $this->add('DELETE', $pattern, $handler);
    }


    /**
     * @return Route[] list of all routes
     */
    public function all()
    {
        return $this->routes;
    }
}