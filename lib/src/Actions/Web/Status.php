<?php

namespace Lib\Actions\Web;

use Lib\Views\User as UserView;

class Status extends AbstractAction
{
    protected function isAuthRequired(): bool
    {
        return false;
    }

    protected function run(): ?array
    {
        if ($this->session->isAuthenticated()) {
            $user = $this->session->getUser();
            $userView = new UserView($user);
            $result = [
                'loggedIn' => true,
                'user' => $userView->render(),
            ];
        } else {
            $result = ['loggedIn' => false];
        }
        return $result;
    }
}