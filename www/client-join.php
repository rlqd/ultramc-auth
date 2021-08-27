<?php require_once(__DIR__ . '/../lib/bootstrap.php');

$action = new \Lib\Actions\ClientJoin();
\Lib\Controller::instance()->run($action);
