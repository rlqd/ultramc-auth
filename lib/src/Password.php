<?php

namespace Lib;

class Password
{
    protected string $hash;

    protected function __construct(string $hash)
    {
        $this->hash = $hash;
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
        return password_verify($password, $this->hash);
    }

    public function getHash() : string
    {
        return $this->hash;
    }
}