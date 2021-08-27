<?php

define('ROOT_DIR', dirname(__DIR__));
define('DATA_DIR', ROOT_DIR . '/data');

require_once __DIR__ . '/vendor/autoload.php';

if (PHP_SAPI !== 'cli') {
    set_exception_handler([\Lib\Controller::instance(), 'handleError']);
}

$dotEnvFile = ROOT_DIR . '/.env';
if (!is_file($dotEnvFile)) {
    throw new \Lib\Exception('Incomplete server configuration');
}
$dotEnv = new Symfony\Component\Dotenv\Dotenv();
$dotEnv->load($dotEnvFile);
