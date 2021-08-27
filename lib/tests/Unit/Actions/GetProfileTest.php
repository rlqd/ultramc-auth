<?php

namespace Tests\Unit\Actions;


use Lib\UUID;

class GetProfileTest extends \Tests\Helpers\ActionTestCase
{
    use \Tests\Helpers\TProfileAssert;

    public function testWithSkin() : void
    {
        $userId = new UUID('4e8b178de057410ca5ee9777339508c3');
        $skinId = new UUID('1ec0368fe2c96720fbe664cdf66f90d2');
        $updated = '2021-01-01 00:00:00';
        $this->mockInputParams(['uuid' => $userId->format()]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->result([
                    'id' => (string) $userId,
                    'name' => 'rlqd',
                    'skin_id' => (string) $skinId,
                ]),
            $this->query(self::OP_SELECT, 'skins')
                ->expect(null, [$skinId])
                ->result([
                    'id' => (string) $skinId,
                    'user_id' => (string) $userId,
                    'updated' => $updated,
                ]),
        );
        $action = new \Lib\Actions\GetProfile();
        self::assertProfileAction(
            $action,
            $userId,
            'rlqd',
            $skinId,
            $updated,
            'cg3oKh4Ai3TZWeBA5B9yC4sCbfcpajYk0KToFlFYATC+xtjQ8W+EtlEHyqel1UgeaMKH7zQHs4B62QWGCYv7exBvDnEMgaPku78N+rdLqzUwO2Fe5jbqcHcJHgBCzE0gjTaxswjbNvw4ir5wxWPEFBYj07D0rH7SB6hj3bxNYTA='
        );
    }

    public function testWithoutSkin() : void
    {
        $userId = new UUID('4e8b178de057410ca5ee9777339508c3');
        $this->mockInputParams(['uuid' => $userId->format()]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->result([
                    'id' => (string) $userId,
                    'name' => 'rlqd',
                ]),
        );
        $action = new \Lib\Actions\GetProfile();
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