<?php require_once(__DIR__ . '/lib/bootstrap.php');

$app = new Symfony\Component\Console\Application();

$app->add(new \Lib\Commands\GenAssetsCertCommand());

$app->run();
