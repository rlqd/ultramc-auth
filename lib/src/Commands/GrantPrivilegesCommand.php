<?php

namespace Lib\Commands;

use Lib\Models\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GrantPrivilegesCommand extends Command
{
    protected const PRIVILEGE_BITS = [
        'approved' => User::BIT_APPROVED,
        'admin' => User::BIT_ADMIN,
    ];

    protected static $defaultName = 'grant-privileges';
    protected static $defaultDescription = 'Set user privileges';

    protected function configure()
    {
        $this->addArgument('username');
        $this->configurePrivileges();
    }

    protected function configurePrivileges()
    {
        foreach (array_keys(self::PRIVILEGE_BITS) as $name) {
            $this->addOption($name, null, null, "Enable '$name' privilege for user");
        }
    }

    protected function getPrivilegeMask(InputInterface $input)
    {
        $privilege_mask = 0;
        foreach (self::PRIVILEGE_BITS as $name => $bit) {
            if ($input->getOption($name)) {
                $privilege_mask |= $bit;
            }
        }
        return $privilege_mask;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('username');
        $users = User::find(['name' => $name]);
        if (empty($users)) {
            $output->writeln("User with name '$name' not found");
            return self::FAILURE;
        }
        $user = reset($users);

        $user->privilege_mask = (string) $this->getPrivilegeMask($input);
        $user->save();

        $output->writeln('Privilege successfully set');
        return self::SUCCESS;
    }
}