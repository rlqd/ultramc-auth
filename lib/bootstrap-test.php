<?php

define('ROOT_DIR', dirname(__DIR__));
define('DATA_DIR', __DIR__ . '/tests/data');

require_once __DIR__ . '/vendor/autoload.php';

$dotEnvFile = ROOT_DIR . '/.env.test';
if (!is_file($dotEnvFile)) {
    throw new \Lib\Exception('Incomplete server configuration (test)');
}
$dotEnv = new Symfony\Component\Dotenv\Dotenv();
$dotEnv->load($dotEnvFile);
