<?php

namespace Lib\Models;

use Lib\DateTime;
use Lib\UUID;

/**
 * @property string $name
 * @property string $password_hash
 * @property string $mojang_uuid
 * @property string $skin_id
 * @property string $privilege_mask
 * @property string $created
 * @property string $last_login
 * @property string $auth_server_id
 */
class User extends AbstractModel
{
    public const SESSION_LIFETIME = 'P30D';

    public const BIT_APPROVED = 0b1;
    public const BIT_ADMIN = 0b10;

    public static function table(): string
    {
        return 'users';
    }

    public function getMojangUuid() : ?UUID
    {
        if ($this->mojang_uuid) {
            return new UUID($this->mojang_uuid);
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
        $lifetime = new \DateInterval(self::SESSION_LIFETIME);
        $since = new DateTime();
        $since->sub($lifetime);
        $sessions = Session::find([
            'id' => $id,
            'since' => ['>=', $since],
        ], 1);
        if (empty($sessions)) {
            throw new \Exception("Session $id not found or expired");
        }
        return reset($sessions);
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
}