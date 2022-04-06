<?php

namespace Lib;


class Input
{
    public const HTTP_GET = 'GET';
    public const HTTP_POST = 'POST';

    use TSingleton;

    public function getParam(string $name) : ?string
    {
        return $_GET[$name] ?? null;
    }

    public function hasParam(string $name) : bool
    {
        return array_key_exists($name, $_GET);
    }

    public function getPost(string $name)
    {
        return $_POST[$name] ?? null;
    }

    public function hasPost(string $name): bool
    {
        return array_key_exists($name, $_POST);
    }

    public function getFile(string $name): ?InputFile
    {
        if ($this->hasFile($name)) {
            return new InputFile($_FILES[$name]);
        }
        return null;
    }

    public function hasFile(string $name): bool
    {
        return array_key_exists($name, $_FILES);
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

    public function getHttpMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? self::HTTP_GET;
    }

    public function isHttpGet(): bool
    {
        return $this->getHttpMethod() === self::HTTP_GET;
    }

    public function isHttpPost(): bool
    {
        return $this->getHttpMethod() === self::HTTP_POST;
    }
}