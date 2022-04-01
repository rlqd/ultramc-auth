<?php

namespace Lib\Actions\Web;

use Lib\Exception;
use Lib\Models\User;
use Lib\Views\User as UserView;
use Lib\Password;

/**
 * POST:
 * @property-read string $username
 * @property-read string $password
 */
class Login extends AbstractAction
{
    protected function isAuthRequired(): bool
    {
        return false;
    }

    protected function handleError(Exception $ex): ?array
    {
        return [
            'success' => false,
            'error' => $ex->getMessage(),
        ];
    }

    protected function run(): ?array
    {
        if (!$this->session->isAuthenticated()) {
            if (!isset($this->username, $this->password)) {
                throw new Exception('Missing required parameters', 400);
            }
            $users = User::find(['name' => $this->username]);
            if (empty($users)) {
                throw new Exception('User not found', 404);
            }
            $user = reset($users);
            $password = $user->getPassword();
            if (!$password->verify($this->password)) {
                throw new Exception('Passwords do not match', 403);
            }
            if ($password->isShouldMigrate()) {
                $user->password_hash = Password::fromPlaintext($this->password)->getHash();
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