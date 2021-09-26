<?php

namespace Lib\Commands;

use Lib\Models\User;
use Lib\Password;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetUserPassword extends Command
{
    protected const PASS_GEN_DICTIONARY = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_';

    protected static $defaultName = 'reset-user-password';
    protected static $defaultDescription = 'Reset password for user';

    protected function configure()
    {
        $this->addOption('settle', null, null, 'Does not require to change the password on first login');
        $this->addArgument('username');
        $this->addArgument('password', InputArgument::OPTIONAL, 'New password (leave empty to generate)');
    }

    protected function generatePassword()
    {
        $password = '';
        for ($i = 0; $i < 12; ++$i) {
            $password .= self::PASS_GEN_DICTIONARY[random_int(0, strlen(self::PASS_GEN_DICTIONARY) - 1)];
        }
        return $password;
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

        $newPassword = $input->getArgument('password');
        if (empty($newPassword)) {
            $newPassword = $this->generatePassword();
            $output->writeln("Generated password: " . $newPassword);
        }

        $user->password_hash = Password::fromPlaintext($newPassword)->getHash();
        if (!$input->getOption('settle')) {
            $user->password_reset = 1;
        }
        $user->save();

        $output->writeln('Password successfully changed' . ($input->getOption('settle') ? ' and settled' : ''));
        return self::SUCCESS;
    }
}