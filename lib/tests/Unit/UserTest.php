<?php

namespace Tests\Unit;

use Lib\Models\User;
use Lib\UUID;
use Tests\Helpers\DbTestCase;

class UserTest extends DbTestCase
{
    public function testLoad() : void
    {
        $id = new UUID('4e8b178de057410ca5ee9777339508c3');
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect()
                ->result([
                    'id' => (string) $id,
                    'name' => 'rlqd',
                    'password_hash' => 'foobar',
                    'mojang_uuid' => '1ec0368fe2c96720fbe664cdf66f90d2',
                    'privilege_mask' => (string) (User::BIT_APPROVED),
                ]),
        );
        $user = User::load($id);
        self::assertObjectEquals($id, $user->getId());
        self::assertEquals('rlqd', $user->name);
        self::assertTrue($user->isApproved(), 'Wrong privilege: isApproved = false');
        self::assertFalse($user->isAdmin(), 'Wrong privilege: isAdmin = true');
    }

    public function testMultipleLoad() : void
    {
        $id1 = new UUID('4e8b178de057410ca5ee9777339508c3');
        $id2 = new UUID('1ec0368fe2c96720fbe664cdf66f90d2');
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect()
                ->result([
                    [
                        'id' => (string) $id1,
                        'name' => 'rlqd',
                        'password_hash' => 'foobar',
                    ],
                    [
                        'id' => (string) $id2,
                        'name' => 'Ask0n',
                        'password_hash' => 'change_me',
                    ],
                ]),
        );
        $users = User::loadAll([$id1, $id2]);
        self::assertCount(2, $users);
        self::assertObjectEquals($id1, $users[0]->getId());
        self::assertObjectEquals($id2, $users[1]->getId());
        self::assertEquals('rlqd', $users[0]->name);
        self::assertEquals('Ask0n', $users[1]->name);
    }

    public function testNotFound() : void
    {
        $id = new UUID('4e8b178de057410ca5ee9777339508c3');
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect()
                ->result(null),
        );
        $this->expectExceptionMessage("Record users:$id not found");
        User::load($id);
    }

    public function testMultipleNotFound() : void
    {
        $id1 = new UUID('4e8b178de057410ca5ee9777339508c3');
        $id2 = new UUID('1ec0368fe2c96720fbe664cdf66f90d2');
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect()
                ->result([
                    [
                        'id' => (string) $id1,
                        'name' => 'rlqd',
                        'password_hash' => 'foobar',
                    ],
                ]),
        );
        $this->expectExceptionMessage("Some `users` records not found: $id2");
        User::loadAll([$id1, $id2]);
    }

    public function providerFind() : array
    {
        return [
            [
                'constraints' => [
                    'auth_server_id' => 'qwerty',
                ],
                'limit' => 1,
                'expectedQuery' => 'SELECT * FROM `users` WHERE `auth_server_id` = :param0 LIMIT 1',
                'expectedParams' => ['param0' => 'qwerty'],
            ],
            [
                'constraints' => [
                    'created' => [
                        ['<>', '2021-01-01 00:00:00'],
                        ['>', '2020-01-01 00:00:00'],
                    ],
                ],
                'limit' => 0,
                'expectedQuery' => 'SELECT * FROM `users` WHERE `created` <> :param0 AND `created` > :param1',
                'expectedParams' => ['param0' => '2021-01-01 00:00:00', 'param1' => '2020-01-01 00:00:00'],
            ],
        ];
    }

    /**
     * @dataProvider providerFind
     * @param array $constraints
     * @param int $limit
     * @param string $expectedQuery
     * @param array $expectedParams
     * @throws \Lib\Exception
     */
    public function testFind(array $constraints, int $limit, string $expectedQuery, array $expectedParams) : void
    {
        $id = new UUID('4e8b178de057410ca5ee9777339508c3');
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect($expectedQuery, $expectedParams)
                ->result([
                    [
                        'id' => (string) $id,
                        'name' => 'rlqd',
                        'password_hash' => 'foobar',
                        'mojang_uuid' => '1ec0368fe2c96720fbe664cdf66f90d2',
                        'privilege_mask' => (string) (User::BIT_APPROVED),
                    ],
                ]),
        );
        $users = User::find($constraints, $limit);
        self::assertCount(1, $users);
        self::assertEquals($id, $users[0]->getId());
    }

    public function testCreate() : void
    {
        $userData = [
            'name' => 'rlqd',
            'password_hash' => 'foobar',
        ];
        $user = User::create($userData);
        $this->mockQueries(
            $this->query(self::OP_INSERT, 'users')
                ->expect('INSERT INTO `users` (`name`, `password_hash`, `id`) VALUES (:name, :password_hash, :id)', ['id' => $user->id] + $userData),
        );
        self::assertTrue($user->isNew());
        $user->save();
        self::assertFalse($user->isNew());
    }

    public function testUpdate() : void
    {
        $id = new UUID('4e8b178de057410ca5ee9777339508c3');
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect()
                ->result([
                    'id' => (string) $id,
                    'name' => 'rlqd',
                    'password_hash' => 'foobar',
                ]),
            $this->query(self::OP_UPDATE, 'users')
                ->expect(
                    'UPDATE `users` SET (`name`, `password_hash`) VALUES (:name, :password_hash) WHERE `id` = :id',
                    ['id' => (string)$id, 'name' => 'rlqd', 'password_hash' => 'change_me']
                ),
        );
        $user = User::load($id);
        self::assertFalse($user->isNew());
        $user->password_hash = 'change_me';
        $user->save();
    }
}