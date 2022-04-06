<?php

namespace Tests\Unit\Actions\Web;

use Lib\Actions\Web\Status;
use Lib\Models\User;
use Lib\UUID;
use Tests\Helpers\ActionTestCase;

class StatusTest extends ActionTestCase
{
    public function testLoggedIn(): void
    {
        $userId = new UUID();
        $skinId = new UUID();
        $skinId2 = new UUID();
        $name = 'rlqd';
        $this->mockInputMethod(self::HTTP_GET);
        $this->mockSessionData(['user_id' => (string) $userId]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect(null, [$userId])
                ->result(
                    [
                        'id' => (string) $userId,
                        'name' => $name,
                        'skin_id' => (string) $skinId,
                        'privilege_mask' => (string) (User::BIT_APPROVED),
                        'password_reset' => 0,
                    ]
                ),
            $this->query(self::OP_SELECT, 'skins')
                ->expect(null, ['param0' => $userId])
                ->result([
                    [
                        'id' => (string) $skinId,
                        'user_id' => (string) $userId,
                    ],
                    [
                        'id' => (string) $skinId2,
                        'user_id' => (string) $userId,
                    ],
                ]),
        );
        $action = new Status();
        self::assertActionOutput($action, [
            'loggedIn' => true,
            'user' => [
                'id' => $userId->format(),
                'name' => $name,
                'mojangUUID' => null,
                'privileges' => [
                    'admin' => false,
                    'approved' => true,
                ],
                'passwordResetRequired' => false,
                'skins' => [
                    [
                        'id' => $skinId->format(),
                        'url' => '/assets/skins/' . $skinId->format() . '.png',
                        'selected' => true,
                    ],
                    [
                        'id' => $skinId2->format(),
                        'url' => '/assets/skins/' . $skinId2->format() . '.png',
                        'selected' => false,
                    ],
                ],
            ],
        ]);
    }

    public function testLoggedOut(): void
    {
        $this->mockInputMethod(self::HTTP_GET);
        $action = new Status();
        self::assertActionOutput($action, [
            'loggedIn' => false,
        ]);
    }
}