<?php

namespace Lib\Actions\Web;

use Lib\Exception;
use Lib\Models\User;
use Lib\Views\User as UserView;
use Lib\Password;

class Login extends AbstractAction
{
    protected function isAuthRequired(): bool
    {
        return false;
    }

    protected function run(): ?array
    {
        if (!$this->session->isAuthenticated()) {
            $input = $this->getInput();
            if (!isset($input['username'], $input['password'])) {
                throw new Exception('missingParameters', Exception::INCORRECT_INPUT);
            }
            $users = User::find(['name' => $input['username']]);
            if (empty($users)) {
                throw new Exception('userNotFound', Exception::NOT_FOUND);
            }
            $user = reset($users);
            $password = $user->getPassword();
            if (!$password->verify($input['password'])) {
                throw new Exception('incorrectPassword', Exception::UNAUTHORIZED);
            }
            if ($password->isShouldMigrate()) {
                $user->password_hash = Password::fromPlaintext($input['password'])->getHash();
                $user->save();
            }

            $this->session->setUser($user);
        }

        $userView = new UserView($this->session->getUser());
        return [
            'success' => true,
            'user' => $userView->render(),
        ];
    }
}