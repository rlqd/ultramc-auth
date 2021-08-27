<?php

namespace Lib\Actions;


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
            throw new \Lib\Exception("User {$user->id} is not approved", 403);
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

    /**
     * @throws \Lib\Exception
     * @throws \JsonException
     */
    protected function getInput(int $depth = 512) : array
    {
        return \Lib\Input::instance()->getInput($depth);
    }
}