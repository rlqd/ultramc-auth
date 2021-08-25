<?php


namespace Lib;


class UUID
{
    private \Symfony\Component\Uid\Uuid $uuid;

    public function __construct(string $uuid = null)
    {
        if ($uuid === null) {
            $this->uuid = \Symfony\Component\Uid\Uuid::v6();
            return;
        }
        $uuid = self::ensureFormat($uuid);
        $this->uuid = \Symfony\Component\Uid\Uuid::fromRfc4122($uuid);
    }

    protected static function ensureFormat(string $uuid) : string
    {
        if($uuid[8] != '-') {
            for ($i = 20; $i >= 8; $i -= 4) {
                $uuid = substr_replace($uuid, '-', $i, 0);
            }
        }
        return $uuid;
    }

    public function format() : string
    {
        return $this->uuid->toRfc4122();
    }

    public function equals(UUID $other) : bool
    {
        return (string) $this === (string) $other;
    }

    public function __toString(): string
    {
        return str_replace('-', '', (string) $this->uuid);
    }
}