<?php

namespace Tests\Unit\Actions\Web;

use Lib\Actions\Web\Status;
use Lib\Models\User;
use Lib\Password;
use Lib\UUID;
use Tests\Helpers\ActionTestCase;

class StatusTest extends ActionTestCase
{
    public function testLoggedIn(): void
    {
        $userId = new UUID();
        $skinId = new UUID();
        $name = 'rlqd';
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
                    ]
                ),
            $this->query(self::OP_SELECT, 'skins')
                ->expect(null, [$skinId])
                ->result([
                    'id' => (string) $skinId,
                    'user_id' => (string) $userId,
                ]),
        );
        $action = new Status();
        self::assertActionOutput($action, [
            'loggedIn' => true,
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
    }

    public function testLoggedOut(): void
    {
        $action = new Status();
        self::assertActionOutput($action, [
            'loggedIn' => false,
        ]);
    }
}