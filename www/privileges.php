<?php require_once(__DIR__ . '/../lib/bootstrap.php');

$action = new \Lib\Actions\GetPrivileges();
\Lib\Controller::instance()->run($action);