<?php

namespace Lib\Commands;

use Lib\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupDatabase extends Command
{
    protected static $defaultName = 'setup-database';
    protected static $defaultDescription = 'Create database tables';

    protected function configure()
    {
        $this->addOption('with-relations', null, null, 'Setup foreign keys on tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $withRelations = $input->getOption('with-relations');
        $db = DB::instance();
        $db->e('CREATE TABLE IF NOT EXISTS `users` (
                `id` char(32) NOT NULL,
                `name` varchar(32) NOT NULL,
                `password_hash` varchar(255) NOT NULL,
                `mojang_uuid` char(32) DEFAULT NULL,
                `skin_id` char(32) DEFAULT NULL,
                `password_reset` tinyint(4) NOT NULL DEFAULT 0,
                `privilege_mask` int(10) NOT NULL DEFAULT 0,
                `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `auth_server_id` varchar(255) DEFAULT NULL,
                `avatar_id` char(32) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`name`)
            )');
        $db->e('CREATE TABLE IF NOT EXISTS `skins` (
                `id` char(32) NOT NULL,
                `user_id` char(32) NOT NULL,
                `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
                ' . ($withRelations ? ',
                KEY `skins_FK` (`user_id`),
                CONSTRAINT `skins_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
            )' : ')'));
        $db->e('CREATE TABLE IF NOT EXISTS `sessions` (
                `id` char(32) NOT NULL,
                `user_id` char(32) NOT NULL,
                `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
                ' . ($withRelations ? ',
                KEY `sessions_FK` (`user_id`),
                CONSTRAINT `sessions_FK` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            )' : ')'));
        return self::SUCCESS;
    }
}