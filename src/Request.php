<?php

namespace CrudeSSG;

class Request
{
    protected string $method;
    protected string $uri;
    protected array $headers;
    protected array $queryParams;
    protected array $routeParams;
    protected array $body;
    protected string $rawBody;
    protected array $cookies;
    protected string $protocol;

    /**
     * Summary of __construct
     * @param array{method: string,uri: string, headers: array,query: array, body: array, rawBody: string, cookies: array, protocol: string, params: string} $config
     */
    public function __construct(array $config)
    {
        $this->method = strtoupper($config['method'] ?? 'GET');
        $this->uri = $config['uri'] ?? '/';
        $this->headers = $config['headers'] ?? [];
        $this->queryParams = $config['query'] ?? [];
        $this->routeParams = $config['params'] ?? [];
        $this->body = $config['body'] ?? [];
        $this->rawBody = $config['rawBody'] ?? '';
        $this->cookies = $config['cookies'] ?? [];
        $this->protocol = $config['protocol'] ?? 'HTTP/1.1';
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $key): ?string
    {
        $keyLower = strtolower($key);
        foreach ($this->headers as $k => $v) {
            if (strtolower($k) === $keyLower) {
                return $v;
            }
        }
        return null;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getQueryParam(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getBodyParam(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    public function getRouteParams()
    {
        return $this->routeParams;
    }

    public function getRouteParam(string $key, mixed $default = null)
    {
        return $this->routeParams[$key] ?? $default;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function getCookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }
}
