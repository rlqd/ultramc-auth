<?php

namespace Tests\Helpers;

use Lib\UUIDFactory;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Component\Uid\Uuid;

class UUIDFactoryMock extends UUIDFactory
{
    protected array $nextUuidList;

    /**
     * @param array<string|Uuid> $nextUuidList
     */
    public function __construct(array $nextUuidList)
    {
        $this->nextUuidList = $nextUuidList;
    }

    public function makeUUID(): Uuid
    {
        $nextUuid = current($this->nextUuidList);
        next($this->nextUuidList);
        if ($nextUuid === false) {
            throw new AssertionFailedError('No more UUIDs to supply, already supplied ' . count($this->nextUuidList));
        }
        if ($nextUuid instanceof \Lib\UUID) {
            $nextUuid = $nextUuid->format();
        }
        if (is_string($nextUuid)) {
            return Uuid::fromString($nextUuid);
        }
        return $nextUuid;
    }
}