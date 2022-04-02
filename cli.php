<?php require_once(__DIR__ . '/lib/bootstrap.php');

$app = new Symfony\Component\Console\Application();

$app->addCommands([
    new \Lib\Commands\GenAssetsCertCommand(),
    new \Lib\Commands\RegisterUserCommand(),
    new \Lib\Commands\GrantPrivilegesCommand(),
    new \Lib\Commands\ResetUserPassword(),
    new \Lib\Commands\SetupDatabase(),
]);

$app->run();
