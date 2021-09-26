<?php

namespace Lib\Models;

use Lib\Exception;
use Lib\Password;
use Lib\UUID;

/**
 * @property string $name
 * @property string $password_hash
 * @property string $password_reset
 * @property string $mojang_uuid
 * @property string $skin_id
 * @property string $avatar_id
 * @property string $privilege_mask
 * @property string $created
 * @property string $auth_server_id
 */
class User extends AbstractModel
{
    public const BIT_APPROVED = 0b1;
    public const BIT_ADMIN = 0b10;

    public static function table(): string
    {
        return 'users';
    }

    public function isLinkedToMojang() : bool
    {
        return !empty($this->mojang_uuid);
    }

    public function getGameUuid() : UUID
    {
        if ($this->mojang_uuid) {
            return new UUID($this->mojang_uuid);
        }
        return $this->getId();
    }

    public function getAvatarId() : ?UUID
    {
        if ($this->avatar_id) {
            return new UUID($this->avatar_id);
        }
        return null;
    }

    public function addPrivileges(int $bits) : void
    {
        $mask = (int) $this->privilege_mask;
        $mask |= $bits;
        $this->privilege_mask = (string) $mask;
    }

    public function hasPrivileges(int $bits) : bool
    {
        $mask = (int) $this->privilege_mask;
        return (bool) ($mask & $bits);
    }

    public function isApproved() : bool
    {
        return $this->hasPrivileges(self::BIT_APPROVED);
    }

    public function isAdmin() : bool
    {
        return $this->hasPrivileges(self::BIT_ADMIN);
    }

    public function getSession(UUID $id) : Session
    {
        $session = Session::load($id);
        if ($session->user_id !== $this->id) {
            throw new Exception('Attempting to load other user session');
        }
        return $session;
    }

    /**
     * @throws \Lib\Exception
     */
    public function getSkin() : ?Skin
    {
        if (!$this->skin_id) {
            return null;
        }
        $skinId = new UUID($this->skin_id);
        return Skin::load($skinId);
    }

    public function getPassword() : Password
    {
        return Password::fromHash($this->password_hash);
    }
}