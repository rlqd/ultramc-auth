<?php require_once(__DIR__ . '/../lib/bootstrap.php');

$action = new \Lib\Actions\ServerJoined();
\Lib\Controller::instance()->run($action);
