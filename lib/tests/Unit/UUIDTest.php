<?php

namespace Tests\Unit;

use Lib\UUID;
use PHPUnit\Framework\TestCase;

class UUIDTest extends TestCase
{
    public function providerFormat() : array
    {
        return [
            'v4' => [
                'raw' => '4e8b178de057410ca5ee9777339508c3',
                'formatted' => '4e8b178d-e057-410c-a5ee-9777339508c3',
            ],
            'v6' => [
                'raw' => '1ec0368fe2c96720fbe664cdf66f90d2',
                'formatted' => '1ec0368f-e2c9-6720-fbe6-64cdf66f90d2',
            ],
        ];
    }

    /**
     * @dataProvider providerFormat
     * @param string $raw
     * @param string $formatted
     */
    public function testFormat(string $raw, string $formatted) : void
    {
        $uuid = new UUID($raw);
        self::assertEquals($raw, (string)$uuid);
        self::assertEquals($formatted, $uuid->format());
    }
}