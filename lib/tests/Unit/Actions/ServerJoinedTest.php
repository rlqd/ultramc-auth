<?php

namespace Tests\Unit\Actions;


use Lib\UUID;

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
                    'param1' => $serverId,
                ])
                ->result([
                    [
                        'id' => (string) $userId,
                        'name' => 'rlqd',
                        'serverId' => $serverId,
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
}