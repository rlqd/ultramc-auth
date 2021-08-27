<?php

namespace Tests\Unit\Actions;


use Lib\UUID;

class GetUsersByNameTest extends \Tests\Helpers\ActionTestCase
{
    public function testSingle() : void
    {
        $id = new UUID('4e8b178de057410ca5ee9777339508c3');
        $this->mockInputParams(['username' => 'rlqd']);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->result([['id' => (string)$id, 'name' => 'rlqd']]),
        );
        $action = new \Lib\Actions\GetUsersByName();
        self::assertActionOutput($action, [
            ['id' => $id->format(), 'name' => 'rlqd']
        ]);
    }

    public function testMultiple() : void
    {
        $id1 = new UUID('4e8b178de057410ca5ee9777339508c3');
        $id2 = new UUID('1ec0368fe2c96720fbe664cdf66f90d2');
        $this->mockInputData(['rlqd', 'Ask0n']);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->result([
                    ['id' => (string)$id1, 'name' => 'rlqd'],
                    ['id' => (string)$id2, 'name' => 'Ask0n'],
                ]),
        );
        $action = new \Lib\Actions\GetUsersByName();
        self::assertActionOutput($action, [
            ['id' => $id1->format(), 'name' => 'rlqd'],
            ['id' => $id2->format(), 'name' => 'Ask0n'],
        ]);
    }
}
