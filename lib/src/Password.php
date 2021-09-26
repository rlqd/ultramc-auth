<?php

namespace Lib;

class Password
{
    protected const LEGACY_PREFIX = '$ultra_l$';

    protected string $hash;
    protected bool $legacy;

    protected function __construct(string $hash)
    {
        $this->hash = $hash;
        $this->legacy = strpos($hash, self::LEGACY_PREFIX) === 0;
    }

    public static function fromHash(string $hash) : self
    {
        return new self($hash);
    }

    public static function fromPlaintext(string $password) : self
    {
        return new self(self::hash($password));
    }

    protected static function hash(string $password) : string
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }

    public function verify(string $password) : bool
    {
        if ($this->legacy) {
            return $this->hash === self::LEGACY_PREFIX . md5(md5($password));
        }
        return password_verify($password, $this->hash);
    }

    public function getHash() : string
    {
        return $this->hash;
    }

    public function isShouldMigrate() : bool
    {
        return $this->legacy;
    }
}