<?php

namespace CrudeSSG;

class HttpHandler
{
    public function __construct(private Renderer $renderer, private Router $router)
    {
    }

    public function handle()
    {
        foreach ($this->router->all() as $route) {
            $routeParams = $route->matches($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
            if ($routeParams) {
                $request = new Request([
                    'method' => $_SERVER['REQUEST_METHOD'],
                    'uri' => $_SERVER['REQUEST_URI'],
                    'query' => $_SERVER['QUERY_STRING'],
                    'cookies' => $_COOKIE,
                    'body' => $_SERVER['REQUEST_METHOD'] == 'GET' ? $_GET : $_POST,
                    'headers' => $_REQUEST['headers'],
                    'params' => $routeParams
                ]);
                $response = $route->handle($request);
                echo $this->renderer->render($response);
                break;
            }
        }
    }
}