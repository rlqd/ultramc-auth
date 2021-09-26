<?php

namespace Tests\Unit\Actions;


use Lib\Models\User;
use Lib\MojangAuth;
use Lib\UUID;
use Tests\Helpers\MojangAuthMock;

class ServerJoinedTest extends \Tests\Helpers\ActionTestCase
{
    use \Tests\Helpers\TProfileAssert;

    public function testJoined() : void
    {
        $userId = new UUID('4e8b178de057410ca5ee9777339508c3');
        $serverId = '123456789';
        $this->mockInputParams(['username' => 'rlqd', 'serverId' => $serverId]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect(null, [
                    'param0' => 'rlqd',
                ])
                ->result([
                    [
                        'id' => (string) $userId,
                        'name' => 'rlqd',
                        'auth_server_id' => $serverId,
                        'privilege_mask' => (string) User::BIT_APPROVED,
                    ],
                ]),
        );
        $action = new \Lib\Actions\ServerJoined();
        self::assertProfileAction(
            $action,
            $userId,
            'rlqd',
            null,
            \Lib\Actions\GetProfile::DEFAULT_TS,
            'iXw1w8IwXf4fHazsUkZev0APGmQzb07JaUDrTYgjkKqr47v+hVC2EB8RFrZuWYCgwFRM0lQBiZEjs5aAg5iW7cr7Tt0D3VeOcs1T4iV/8EYE1Ki/9XwZ9nNVIjeTRpVk5TLJBEV4yJ6IehdG0nBpJ71eGICzbSYJ6vvtuOcerGE='
        );
    }

    public function testMojang() : void
    {
        $mockResponse = ['qwerty' => 'uiop'];
        $this->mockSingleton(MojangAuth::class, new MojangAuthMock($mockResponse));
        $userId = new UUID('4e8b178de057410ca5ee9777339508c3');
        $serverId = '123456789';
        $this->mockInputParams(['username' => 'rlqd', 'serverId' => $serverId]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect(null, [
                    'param0' => 'rlqd',
                ])
                ->result([
                    [
                        'id' => (string) $userId,
                        'name' => 'rlqd',
                        'auth_server_id' => 'non matching id',
                        'privilege_mask' => (string) User::BIT_APPROVED,
                        'mojang_uuid' => (string) (new UUID()),
                    ],
                ]),
        );
        $action = new \Lib\Actions\ServerJoined();
        self::assertActionOutput($action, $mockResponse);
    }
}