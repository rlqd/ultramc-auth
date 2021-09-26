<?php

namespace Tests\Helpers;

use Lib\MojangAuth;

class MojangAuthMock extends MojangAuth
{
    protected array $result;

    public function __construct(array $result)
    {
        $this->result = $result;
    }

    public function serverJoined(string $username, string $serverId): array
    {
        return $this->result;
    }
}