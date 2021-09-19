<?php

namespace Lib\Actions\Web;

use Lib\Exception;
use Lib\Models\User;
use Lib\WebRedirect;

/**
 * POST:
 * @property-read string $username
 * @property-read string $password
 * @property-read string $return_url
 */
class Login extends AbstractAction
{
    protected function isAuthRequired(): bool
    {
        return false;
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
            if (!$user->getPassword()->verify($this->password)) {
                throw new Exception('Passwords do not match', 403);
            }
            $this->session->setUser($user);
        }
        throw new WebRedirect($this->return_url ?? '/');
    }
}