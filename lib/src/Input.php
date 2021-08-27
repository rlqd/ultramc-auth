<?php

namespace Lib;


class Input
{
    use TSingleton;

    public function getParam(string $name) : ?string
    {
        return $_GET[$name] ?? null;
    }

    public function hasParam(string $name) : bool
    {
        return array_key_exists($name, $_GET);
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function getInput(int $depth = 512) : array
    {
        $input = json_decode(file_get_contents('php://input'), true, $depth, JSON_THROW_ON_ERROR);
        if (!is_array($input)) {
            throw new Exception('Wrong input', 400);
        }
        return $input;
    }
}