<?php

namespace Tests\Unit\Actions\Web;

use Lib\Actions\Web\Login;
use Lib\Models\User;
use Lib\Password;
use Lib\UUID;
use Tests\Helpers\ActionTestCase;

class LoginTest extends ActionTestCase
{
    public function testSuccess() : void
    {
        $userId = new UUID();
        $skinId = new UUID();
        $name = 'rlqd';
        $password = 'hackme';
        $this->mockInputPost([
            'username' => $name,
            'password' => $password,
        ]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect(null, ['param0' => $name])
                ->result(
                    [
                        [
                            'id' => (string) $userId,
                            'name' => $name,
                            'password_hash' => Password::fromPlaintext($password)->getHash(),
                            'skin_id' => (string) $skinId,
                            'privilege_mask' => (string) (User::BIT_APPROVED),
                        ],
                    ]
                ),
            $this->query(self::OP_SELECT, 'skins')
                ->expect(null, [$skinId])
                ->result([
                    'id' => (string) $skinId,
                    'user_id' => (string) $userId,
                ]),
        );
        $action = new Login();
        self::assertActionOutput($action, [
            'success' => true,
            'user' => [
                'id' => $userId->format(),
                'name' => $name,
                'mojangUUID' => null,
                'skinUrl' => '/assets/skins/' . $skinId->format() . '.png',
                'privileges' => [
                    'admin' => false,
                    'approved' => true,
                ],
            ],
        ]);
        self::assertEquals((string)$userId, $this->getSession()->user_id);
    }

    public function testFailure() : void
    {
        $userId = new UUID();
        $name = 'rlqd';
        $password = 'hackme';
        $this->mockInputPost([
            'username' => $name,
            'password' => 'wrong',
        ]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect(null, ['param0' => $name])
                ->result(
                    [
                        [
                            'id' => (string) $userId,
                            'name' => $name,
                            'password_hash' => Password::fromPlaintext($password)->getHash(),
                        ],
                    ]
                )
        );
        $action = new Login();
        self::assertActionOutput($action, [
            'success' => false,
            'error' => 'Passwords do not match',
        ]);
        self::assertNotEquals((string)$userId, $this->getSession()->user_id);
    }
}