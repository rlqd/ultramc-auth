<?php

define('ROOT_DIR', dirname(__DIR__));
define('TESTS_DIR', __DIR__ . '/tests');
define('DATA_DIR', TESTS_DIR . '/data');
define('ASSETS_DIR', TESTS_DIR . '/assets');

require_once __DIR__ . '/vendor/autoload.php';

$dotEnvFile = ROOT_DIR . '/.env.test';
if (!is_file($dotEnvFile)) {
    throw new \Lib\Exception('Incomplete server configuration (test)');
}
$dotEnv = new Symfony\Component\Dotenv\Dotenv();
$dotEnv->load($dotEnvFile);
