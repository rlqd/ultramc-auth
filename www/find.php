<?php require_once(__DIR__ . '/../lib/bootstrap.php');

$action = new \Lib\Actions\GetUsersByName();
\Lib\Controller::instance()->run($action);
