<?php

namespace Lib;


class Logger
{
    use TSingleton;

    public function error(\Throwable $t)
    {
        $logMessage = date('[Y-m-d H:i:s] ') . addslashes((string) $t) . "\n";
        error_log($logMessage, 3, DATA_DIR . '/auth-errors.log');
    }
}