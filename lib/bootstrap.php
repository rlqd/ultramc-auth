<?php

define('ROOT_DIR', dirname(__DIR__));
define('DATA_DIR', ROOT_DIR . '/data');

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotEnvFile = ROOT_DIR . '/.env';
if (!is_file($dotEnvFile)) {
    throw new \Exception('Incomplete server configuration');
}
$dotEnv = new Dotenv();
$dotEnv->load($dotEnvFile);
