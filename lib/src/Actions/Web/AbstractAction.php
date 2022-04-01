<?php

namespace Lib\Actions\Web;

use Lib\Exception;

abstract class AbstractAction extends \Lib\Actions\AbstractAction
{
    protected \Lib\WebSession $session;
    protected ?\Lib\Models\User $currentUser = null;

    public function __construct()
    {
        $this->session = \Lib\WebSession::instance();
    }

    public function call(): ?array
    {
        $this->session->init();
        if ($this->isAuthRequired()) {
            $this->authenticate();
        }
        try {
            return parent::call();
        } catch (Exception $ex) {
            if ($ex->isInternal()) {
                throw $ex;
            }
            return $this->handleError($ex);
        }
    }

    protected function handleError(Exception $ex): ?array
    {
        throw $ex;
    }

    protected function isAuthRequired(): bool
    {
        return true;
    }

    /**
     * @throws \Lib\Exception
     */
    protected function authenticate(): void
    {
        $this->currentUser = $this->session->getUser();
        if ($this->currentUser === null) {
            throw new \Lib\Exception('Session not authenticated', 401);
        }
        $this->checkAccess($this->currentUser);
    }
}