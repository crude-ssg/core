<?php

namespace CrudeSSG;

class DataCollection
{
    public function __construct(private array $list)
    {
    }

    public function all(): array
    {
        return $this->list;
    }

    public function where(string $key, mixed $value): static
    {
        $filtered = array_filter($this->list, function ($item) use ($key, $value) {
            return is_array($item) && ($item[$key] ?? null) === $value;
        });

        return new static(array_values($filtered));
    }

    public function filter(callable $callback): static
    {
        $filtered = array_filter($this->list, $callback, ARRAY_FILTER_USE_BOTH);
        return new static(array_values($filtered));
    }

    public function map(callable $callback): static
    {
        $mapped = array_map($callback, $this->list);
        return new static($mapped);
    }

    public function first(): mixed
    {
        return $this->list[0] ?? null;
    }

    public function pluck(string $key): array
    {
        return array_map(function ($item) use ($key) {
            return is_array($item) ? ($item[$key] ?? null) : null;
        }, $this->list);
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->list, $options);
    }

    public function count(): int
    {
        return count($this->list);
    }

    public function isEmpty(): bool
    {
        return empty($this->list);
    }

    public function wire(string $routeParameter, string $attribute): DataCollection
    {
        return $this->map(fn($item) => [
            $routeParameter => $item[$attribute],
        ]);
    }

    public function wireWith(string $routeParameter, string $attribute, string $childRouteParameter, string $childAttribute, string $viaCollection)
    {
        return $this->map(fn($item) => [
            $routeParameter => $item[$attribute],
            $childRouteParameter => Data::load($viaCollection)->where($routeParameter, $item[$attribute])->pluck($childAttribute)
        ])->all();
    }
}
