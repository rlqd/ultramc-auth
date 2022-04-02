<?php

namespace Lib;


class Exception extends \Exception
{
    public const INCORRECT_INPUT = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const INTERNAL = 500;

    protected bool $isInternal;

    public function __construct($message = "", $code = self::INTERNAL, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->isInternal = $code < 400 || $code >= 500;
    }

    public function isInternal() : bool
    {
        return $this->isInternal;
    }
}