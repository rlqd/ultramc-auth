<?php

namespace Tests\Helpers;


class EchoAction implements \Lib\IAction
{
    private ?array $output;

    public function __construct(?array $output)
    {
        $this->output = $output;
    }

    public function call(): ?array
    {
        return $this->output;
    }
}