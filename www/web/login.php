<?php require_once(__DIR__ . '/../../lib/bootstrap.php');

$action = new \Lib\Actions\Web\Login();
\Lib\Controller::instance()->run($action);
