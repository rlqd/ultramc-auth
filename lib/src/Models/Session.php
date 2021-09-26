<?php

namespace Lib\Models;


use Lib\DateTime;
use Lib\UUID;

/**
 * @property-read string $user_id
 * @property string $updated
 */
class Session extends AbstractModel
{
    public const SESSION_LIFETIME = 'P30D';

    public static function table(): string
    {
        return 'sessions';
    }

    public static function load(UUID $id, bool $filterExpired = true): self
    {
        if (!$filterExpired) {
            return parent::load($id);
        }
        $lifetime = new \DateInterval(self::SESSION_LIFETIME);
        $since = new DateTime();
        $since->sub($lifetime);
        $sessions = static::find([
            'id' => $id,
            'updated' => ['>=', $since],
        ], 1);
        if (empty($sessions)) {
            throw new \Exception("Session $id not found or expired", 403);
        }
        return reset($sessions);
    }

    public function readonly(): array
    {
        $readonly = parent::readonly();
        $readonly[] = 'user_id';
        return $readonly;
    }

    /**
     * @throws \Lib\Exception
     */
    public function getUser() : User
    {
        return User::load($this->getUserId());
    }

    public function getUserId() : \Lib\UUID
    {
        return new \Lib\UUID($this->user_id);
    }

    /**
     * Refresh session updated datetime
     * @throws \Lib\Exception
     */
    public function touch() : void
    {
        $this->updated = new \Lib\DateTime();
        $this->save();
    }
}