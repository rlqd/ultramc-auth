<?php

namespace Tests\Helpers;


class InputMock extends \Lib\Input
{
    private array $params = [];
    private ?array $input = null;

    public function getParam(string $name): ?string
    {
        return $this->params[$name] ?? null;
    }

    public function hasParam(string $name) : bool
    {
        return array_key_exists($name, $this->params);
    }

    public function getInput(int $depth = 512): array
    {
        if ($this->input === null) {
            throw new \Lib\Exception('Wrong input', 400);
        }
        return $this->input;
    }

    public function setParams(array $params) : void
    {
        $this->params = $params;
    }

    public function setInput(array $input) : void
    {
        $this->input = $input;
    }
}