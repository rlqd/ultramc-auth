<?php

namespace Lib\Commands;

use Lib\Models\User;
use Lib\Password;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterUserCommand extends GrantPrivilegesCommand
{
    protected static $defaultName = 'register-user';
    protected static $defaultDescription = 'Register a new user';

    protected function configure()
    {
        $this->addArgument('username');
        $this->addArgument('password');
        $this->configurePrivileges();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = User::create([
            'name' => $input->getArgument('username'),
            'password_hash' => Password::fromPlaintext($input->getArgument('password'))->getHash(),
            'privilege_mask' => $this->getPrivilegeMask($input),
        ]);
        $user->save();
        $output->writeln("User created: $user->id");
        return self::SUCCESS;
    }
}