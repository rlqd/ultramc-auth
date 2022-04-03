<?php

namespace Lib\Actions\Web;

class Logout extends AbstractAction
{
    protected function isAuthRequired(): bool
    {
        return false;
    }

    protected function run(): ?array
    {
        $this->session->resetUser();
        return [
            'success' => true,
        ];
    }
}