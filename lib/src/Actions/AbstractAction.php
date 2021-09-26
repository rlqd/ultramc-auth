<?php

namespace Lib\Actions;


use Lib\Exception;

abstract class AbstractAction implements \Lib\IAction
{
    /**
     * @return array|null
     * @throws \Lib\Exception
     */
    abstract protected function run() : ?array;

    public function call(): ?array
    {
        return $this->run();
    }

    protected function loadActiveUser(\Lib\UUID $id) : \Lib\Models\User
    {
        $user = \Lib\Models\User::load($id);
        $this->checkAccess($user);
        return $user;
    }

    protected function checkAccess(\Lib\Models\User $user) : void
    {
        if (!$user->isApproved()) {
            throw new Exception("User is not approved", 403);
        }
        if ($user->password_reset) {
            throw new Exception("User has temporary password, game auth not permitted", 403);
        }
    }

    protected function getParam(string $name) : ?string
    {
        return \Lib\Input::instance()->getParam($name);
    }

    protected function hasParam(string $name) : bool
    {
        return \Lib\Input::instance()->hasParam($name);
    }

    protected function getPost(string $name) : ?string
    {
        return \Lib\Input::instance()->getPost($name);
    }

    protected function hasPost(string $name) : bool
    {
        return \Lib\Input::instance()->hasPost($name);
    }

    /**
     * @throws \Lib\Exception
     * @throws \JsonException
     */
    protected function getInput(int $depth = 2) : array
    {
        return \Lib\Input::instance()->getInput($depth);
    }

    public function __get($name)
    {
        return $this->getParam($name) ?? $this->getPost($name);
    }

    public function __isset($name)
    {
        return $this->hasParam($name) || $this->hasPost($name);
    }
}