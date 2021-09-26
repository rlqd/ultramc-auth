<?php

namespace Tests\Unit\Actions;

use Lib\Actions\Authenticate;
use Lib\Models\User;
use Lib\Password;
use Lib\UUID;
use Lib\UUIDFactory;
use Tests\Helpers\ActionTestCase;
use Tests\Helpers\UUIDFactoryMock;

class AuthenticateTest extends ActionTestCase
{
    public function testLogin() : void
    {
        $userId = new UUID();
        $avatarId = new UUID('88f4fa48-6cf1-4966-bd04-e653537bb089');
        $sessionId = new UUID();
        $name = 'rlqd';
        $password = 'hackme';
        $this->mockInputData([
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
                            'avatar_id' => (string) $avatarId,
                            'privilege_mask' => (string) User::BIT_APPROVED,
                        ],
                    ]
                ),
            $this->query(self::OP_INSERT, 'sessions')
                ->expect(null, ['user_id' => $userId])
                ->result(1),
        );
        $this->mockSingleton(UUIDFactory::class, new UUIDFactoryMock([$sessionId]));
        $action = new Authenticate();
        $this->assertActionOutput($action, [
            'uuid' => $userId->format(),
            'name' => $name,
            'avatarImage' => base64_encode(file_get_contents(TESTS_DIR . '/assets/avatars/88f4fa48-6cf1-4966-bd04-e653537bb089.jpg')),
            'accessToken' => (string)$sessionId,
        ]);
    }

    public function testPasswordMigrate() : void
    {
        $userId = new UUID();
        $avatarId = new UUID('88f4fa48-6cf1-4966-bd04-e653537bb089');
        $sessionId = new UUID();
        $name = 'rlqd';
        $password = 'hackme';
        $this->mockInputData([
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
                            'password_hash' => '$ultra_l$0bcd5ad3fe8307708e4b2f9c0f98ae97',
                            'avatar_id' => (string) $avatarId,
                            'privilege_mask' => (string) User::BIT_APPROVED,
                        ],
                    ]
                ),
            $this->query(self::OP_UPDATE, 'users')
                ->expect()
                ->result(1),
            $this->query(self::OP_INSERT, 'sessions')
                ->expect(null, ['user_id' => $userId])
                ->result(1),
        );
        $this->mockSingleton(UUIDFactory::class, new UUIDFactoryMock([$sessionId]));
        $action = new Authenticate();
        $this->assertActionOutput($action, [
            'uuid' => $userId->format(),
            'name' => $name,
            'avatarImage' => base64_encode(file_get_contents(TESTS_DIR . '/assets/avatars/88f4fa48-6cf1-4966-bd04-e653537bb089.jpg')),
            'accessToken' => (string)$sessionId,
        ]);
    }

    public function testRefresh() : void
    {
        $userId = new UUID();
        $sessionId = new UUID();
        $name = 'rlqd';
        $this->mockInputParams(['refresh' => null]);
        $this->mockInputData([
            'token' => (string) $sessionId,
        ]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'sessions')
                ->expect(null, ['param0' => (string) $sessionId])
                ->result([
                    [
                        'id' => (string) $sessionId,
                        'user_id' => (string) $userId,
                    ],
                ]),
            $this->query(self::OP_SELECT, 'users')
                ->expect(null, [(string) $userId])
                ->result([
                    'id' => (string) $userId,
                    'name' => $name,
                    'privilege_mask' => (string) User::BIT_APPROVED,
                ]),
            $this->query(self::OP_UPDATE, 'sessions')
                ->result(1)
        );
        $action = new Authenticate();
        $this->assertActionOutput($action, [
            'uuid' => $userId->format(),
            'name' => $name,
            'avatarImage' => '',
            'accessToken' => (string)$sessionId,
        ]);
    }
}