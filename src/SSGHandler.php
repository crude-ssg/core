<?php

namespace CrudeSSG;

class SSGHandler
{
    public function __construct(
        private Renderer $renderer,
        private Router $router,
        private string $outputDir = 'public',
        private string $assetsDir = 'assets'
    ) {
    }

    public function handle()
    {
        $this->collectPages();
        $this->collectAssets();
    }

    private function collectPages()
    {
        foreach ($this->router->all() as $route) {
            if ($route->getMethod() != 'GET') {
                continue;
            }

            $this->renderRoutePages($route);
        }
    }

    function renderRoutePages(Route $route)
    {
        if (!$route->isSsgEnabled()) {
            return [];
        }

        $paramsList = $route->getSsgParams();
        if (count($paramsList) == 0) {
            $request = new Request([
                'method' => $route->getMethod(),
                'uri' => $route->getOriginalPattern(),
                'params' => [],
            ]);

            $this->renderPage($route, $request);
            return;
        }

        // Expand combinations
        $expandedParamsList = [];
        foreach ($paramsList as $params) {
            $expanded = $this->expandParams($params);
            foreach ($expanded as $combo) {
                $expandedParamsList[] = $combo;
            }
        }

        foreach ($expandedParamsList as $params) {
            $uri = $route->getOriginalPattern();

            foreach ($params as $key => $value) {
                $uri = str_replace("{{$key}}", $value, $uri);
            }

            $request = new Request([
                'method' => $route->getMethod(),
                'uri' => $uri,
                'params' => $params,
            ]);

            $this->renderPage($route, $request);
        }
    }

    function renderPage(Route $route, Request $request)
    {
        try {
            $response = $route->handle($request);
            if ($response instanceof Page) {
                $this->compile($response, $request);
                printf("✅ %s\n", $request->getUri());
            } else {
                printf("⚠️  %s - Must return an instance of Page::class \n", $request->getUri());
            }
        } catch (\Throwable $e) {
            printf("⚠️  %s - %s\n", $request->getUri(), $e->getMessage());
        }
    }

    function collectAssets()
    {
        // Read all files from assetsDir and copy to outputDir/assets
        $sourceDir = $this->assetsDir;
        $destDir = $this->outputDir . DIRECTORY_SEPARATOR . 'assets';

        // Create destination directory if it doesn't exist
        FsUtil::ensureDir($destDir);
        FsUtil::rrmdir($destDir);
        FsUtil::rcopy($sourceDir, $destDir);
    }

    private function compile(Page $page, Request $req)
    {
        $output = $this->renderer->render($page);
        $url = rtrim(ltrim($req->getUri(), DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
        $outputPath = $this->outputDir . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR . "index.html";
        // if ($url == "") {
        //     $outputPath = $this->outputDir . DIRECTORY_SEPARATOR . "index.html";
        // }
        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, recursive: true);
        }
        file_put_contents($outputPath, $output);
    }

    /**
     * Expand params where any value is an array into multiple param sets.
     *
     * Example:
     * ['shop' => 'a', 'product' => ['p1', 'p2']]
     * =>
     * [['shop' => 'a', 'product' => 'p1'], ['shop' => 'a', 'product' => 'p2']]
     */
    private function expandParams(array $params): array
    {
        $keys = array_keys($params);
        $values = array_values($params);

        // Normalize all values to arrays for cartesian product
        $normalized = array_map(function ($v) {
            return is_array($v) ? $v : [$v];
        }, $values);

        // Cartesian product
        $combinations = $this->cartesianProduct($normalized);

        // Recombine with keys
        return array_map(function ($combo) use ($keys) {
            return array_combine($keys, $combo);
        }, $combinations);
    }

    /**
     * Generate cartesian product of arrays.
     *
     * Example:
     * [[a, b], [1, 2]] => [[a,1], [a,2], [b,1], [b,2]]
     */
    private function cartesianProduct(array $arrays): array
    {
        $result = [[]];

        foreach ($arrays as $propertyValues) {
            $tmp = [];
            foreach ($result as $product) {
                foreach ($propertyValues as $value) {
                    $tmp[] = array_merge($product, [$value]);
                }
            }
            $result = $tmp;
        }

        return $result;
    }
}