<?php

namespace Lib;


class Exception extends \Exception
{
    protected bool $isInternal;

    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->isInternal = $code < 400 || $code >= 500;
    }

    public function isInternal() : bool
    {
        return $this->isInternal;
    }
}