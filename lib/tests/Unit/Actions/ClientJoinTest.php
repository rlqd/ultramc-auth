<?php

namespace Tests\Unit\Actions;


use Lib\UUID;

class ClientJoinTest extends \Tests\Helpers\ActionTestCase
{
    public function testJoin() : void
    {
        $userId = new UUID('4e8b178de057410ca5ee9777339508c3');
        $token = new UUID();
        $serverId = '123456789';
        $this->mockInputData([
            'selectedProfile' => $userId->format(),
            'accessToken' => (string) $token,
            'serverId' => $serverId,
        ]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->result([
                    'id' => (string) $userId,
                    'name' => 'rlqd',
                    'privilege_mask' => (string) \Lib\Models\User::BIT_APPROVED,
                ]),
            $this->query(self::OP_SELECT, 'sessions')
                ->result([
                    [
                        'id' => (string) $token,
                        'user_id' => (string) $userId,
                    ],
                ]),
            $this->query(self::OP_UPDATE, 'sessions')
                ->expect()
                ->result(1),
            $this->query(self::OP_UPDATE, 'users')
                ->expect(null, ['id' => (string) $userId, 'auth_server_id' => $serverId])
                ->result(1),
        );
        self::assertActionOutput(new \Lib\Actions\ClientJoin(), null);
    }
}