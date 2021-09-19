<?php

namespace Lib;

class UUIDFactory
{
    use TSingleton;

    public function makeUUID() : \Symfony\Component\Uid\Uuid
    {
        return \Symfony\Component\Uid\Uuid::v6();
    }
}