<?php

namespace Lib;


/**
 * @property int $user_id
 */
class WebSession
{
    use TSingleton;

    private const SESSION_OPTIONS = [
        'cookie_lifetime' => 86400,
    ];

    private ?\Lib\Models\User $user = null;

    public function init() : void
    {
        $res = session_start(self::SESSION_OPTIONS);
        if (!$res) {
            throw new \Lib\Exception('Failed to start web session');
        }
    }

    public function __get($name)
    {
        return $_SESSION[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * @return Models\User|null
     * @throws Exception
     */
    public function getUser(): ?Models\User
    {
        if (!$this->user_id) {
            return null;
        }
        if (!$this->user) {
            $userId = new UUID($this->user_id);
            $this->user = \Lib\Models\User::load($userId);
        }
        return $this->user;
    }
}