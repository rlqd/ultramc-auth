<?php

namespace Tests\Unit\Actions\Web;

use Lib\Actions\Web\Login;
use Lib\Password;
use Lib\UUID;
use Lib\WebRedirect;
use Tests\Helpers\ActionTestCase;

class LoginTest extends ActionTestCase
{
    public function testSuccess() : void
    {
        $userId = new UUID();
        $name = 'rlqd';
        $password = 'hackme';
        $return = '/kitty-images';
        $this->mockInputPost([
            'username' => $name,
            'password' => $password,
            'return_url' => $return,
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
        try {
            $this->callAction($action);
        } catch (WebRedirect $redirect) {
            self::assertEquals(['Location' => $return], $redirect->getHeaders());
        }
        self::assertEquals((string)$userId, $this->getSession()->user_id);
    }

    public function testFailure() : void
    {
        $userId = new UUID();
        $name = 'rlqd';
        $password = 'hackme';
        $return = '/kitty-images';
        $this->mockInputPost([
            'username' => $name,
            'password' => 'wrong',
            'return_url' => $return,
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
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('Passwords do not match');
        $action = new Login();
        $this->callAction($action);
    }
}