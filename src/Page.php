<?php

namespace CrudeSSG;

class Page
{

    public string $template;
    private array $data = [];
    private array $params = [];

    public function __construct(string $template)
    {
        $this->template = $template;
    }

    public static function make(string $template)
    {
        return new Page($template);
    }

    public function withParam(string $name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    public function with(string $name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getContext()
    {
        return $this->data;
    }
}