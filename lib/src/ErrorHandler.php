<?php

namespace Lib;

class ErrorHandler
{
    public static function process(\Throwable $error): array
    {
        if ($error instanceof Exception && !$error->isInternal()) {
            $code = $error->getCode();
            $message = $error->getMessage();
        } else {
            $code = 500;
            $message = 'Internal server error';
        }
        if ($code == 500) {
            Logger::instance()->error($error);
        }
        return [$code, $message];
    }
}