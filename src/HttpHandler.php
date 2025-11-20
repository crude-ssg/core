<?php

namespace CrudeSSG;

class HttpHandler
{
    public function __construct(private Renderer $renderer, private Router $router)
    {
    }

    public function handle()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $parsed = parse_url($uri);
        $path = $parsed['path'];
        $query_params = [];
        parse_str($parsed['query'] ?? '', $query_params);

        foreach ($this->router->all() as $route) {
            $routeParams = $route->matches($_SERVER['REQUEST_METHOD'], $path);
            if (isset($routeParams)) {
                $request = new Request([
                    'method' => $_SERVER['REQUEST_METHOD'],
                    'uri' => $_SERVER['REQUEST_URI'],
                    'query' => $query_params,
                    'cookies' => $_COOKIE,
                    'body' => $_SERVER['REQUEST_METHOD'] == 'GET' ? $_GET : $_POST,
                    'headers' => getallheaders(),
                    'params' => $routeParams
                ]);
                $response = $route->handle($request);
                echo $this->renderer->render($response);
                break;
            }
        }
    }
}