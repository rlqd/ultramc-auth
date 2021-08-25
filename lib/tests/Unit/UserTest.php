<?php

namespace Tests\Unit;

use Lib\User;
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
        self::assertEquals($user->name, 'rlqd');
        self::assertTrue($user->isApproved(), 'Wrong privilege: isApproved = false');
        self::assertFalse($user->isAdmin(), 'Wrong privilege: isAdmin = true');
    }

    public function testNotFound()
    {
        $id = new UUID('4e8b178de057410ca5ee9777339508c3');
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect()
                ->result(null),
        );
        $this->expectExceptionMessage("User $id not found");
        User::load($id);
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
                ->expect('INSERT INTO `users` (`id`, `name`, `password_hash`) VALUES (:id, :name, :password_hash)', ['id' => $user->id] + $userData),
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