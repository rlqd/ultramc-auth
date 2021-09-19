<?php

namespace Tests\Helpers;

use Lib\WebSession;

class WebSessionMock extends WebSession
{
    protected array $data;

    public function init(): void
    {
        //do nothing
    }

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function setData(array $data) : void
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}