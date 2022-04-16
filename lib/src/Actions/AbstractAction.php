<?php

namespace Lib\Actions;


use Lib\Exception;
use Lib\Input;

abstract class AbstractAction implements \Lib\IAction
{
    protected const HTTP_GET = Input::HTTP_GET;
    protected const HTTP_POST = Input::HTTP_POST;

    protected const CHECK_PASSWORD = 'password';
    protected const CHECK_PRIVILEGE = 'privilege';

    /**
     * @return array|null
     * @throws \Lib\Exception
     */
    abstract protected function run() : ?array;

    public function call(): ?array
    {
        if (!in_array($this->getHttpMethod(), $this->getAcceptedMethods(), true)) {
            throw new Exception('Method not allowed', Exception::WRONG_METHOD);
        }
        return $this->run();
    }

    protected function getAcceptedMethods(): array
    {
        return [self::HTTP_GET, self::HTTP_POST];
    }

    protected function loadActiveUser(\Lib\UUID $id): \Lib\Models\User
    {
        $user = \Lib\Models\User::load($id);
        $this->checkAccess($user);
        return $user;
    }

    protected function getAccessChecks(): array
    {
        return [
            [self::CHECK_PASSWORD],
            [self::CHECK_PRIVILEGE, \Lib\Models\User::BIT_APPROVED],
        ];
    }

    protected function checkAccess(\Lib\Models\User $user): void
    {
        foreach ($this->getAccessChecks() as $check) {
            switch ($check[0]) {
                case self::CHECK_PASSWORD:
                    if ($user->password_reset) {
                        throw new Exception("User has temporary password, action not permitted", Exception::FORBIDDEN);
                    }
                    break;
                case self::CHECK_PRIVILEGE:
                    if (!$user->hasPrivileges($check[1])) {
                        throw new Exception("User is not privileged", Exception::FORBIDDEN);
                    }
                    break;
            }
        }
    }

    protected function getParam(string $name): ?string
    {
        return \Lib\Input::instance()->getParam($name);
    }

    protected function hasParam(string $name): bool
    {
        return \Lib\Input::instance()->hasParam($name);
    }

    protected function getPost(string $name): ?string
    {
        return \Lib\Input::instance()->getPost($name);
    }

    protected function hasPost(string $name): bool
    {
        return \Lib\Input::instance()->hasPost($name);
    }

    protected function getFile(string $name): ?\Lib\InputFile
    {
        return \Lib\Input::instance()->getFile($name);
    }

    protected function hasFile(string $name): bool
    {
        return \Lib\Input::instance()->hasFile($name);
    }

    /**
     * @throws \Lib\Exception
     * @throws \JsonException
     */
    protected function getInput(int $depth = 2): array
    {
        return \Lib\Input::instance()->getInput($depth);
    }

    protected function getHttpMethod(): string
    {
        return Input::instance()->getHttpMethod();
    }

    public function isHttpGet(): bool
    {
        return Input::instance()->isHttpGet();
    }

    public function isHttpPost(): bool
    {
        return Input::instance()->isHttpPost();
    }

    public function __get($name)
    {
        return $this->getParam($name) ?? $this->getPost($name) ?? $this->getFile($name);
    }

    public function __isset($name)
    {
        return $this->hasParam($name) || $this->hasPost($name) || $this->hasFile($name);
    }
}