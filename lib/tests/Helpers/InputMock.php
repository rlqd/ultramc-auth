<?php

namespace Tests\Helpers;


class InputMock extends \Lib\Input
{
    private array $params = [];
    private array $post = [];
    private array $files = [];
    private array $savedFiles = [];
    private ?array $input = null;
    private string $method = self::HTTP_POST;

    public function getParam(string $name): ?string
    {
        return $this->params[$name] ?? null;
    }

    public function hasParam(string $name) : bool
    {
        return array_key_exists($name, $this->params);
    }

    public function getPost(string $name)
    {
        return $this->post[$name] ?? null;
    }

    public function hasPost(string $name) : bool
    {
        return array_key_exists($name, $this->post);
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

    public function setPost(array $post) : void
    {
        $this->post = $post;
    }

    public function setInput(array $input) : void
    {
        $this->input = $input;
    }

    public function getHttpMethod(): string
    {
        return $this->method;
    }

    public function setHttpMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getFile(string $name): ?\Lib\InputFile
    {
        if ($this->hasFile($name)) {
            return new InputFileMock($this, $this->files[$name]);
        }
        return null;
    }

    public function hasFile(string $name): bool
    {
        return array_key_exists($name, $this->files);
    }

    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    public function logFileUpload(string $tmpName, string $destination): void
    {
        $this->savedFiles[$tmpName] = $destination;
    }

    public function getSavedFiles(): array
    {
        return $this->savedFiles;
    }
}
