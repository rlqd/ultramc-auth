<?php

namespace Tests\Helpers;


use Lib\WebSession;

class ActionTestCase extends DbTestCase
{
    protected const HTTP_GET = \Lib\Input::HTTP_GET;
    protected const HTTP_POST = \Lib\Input::HTTP_POST;

    private InputMock $inputMock;
    private WebSessionMock $sessionMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inputMock = new InputMock();
        $this->mockSingleton(\Lib\Input::class, $this->inputMock);
        $this->sessionMock = new WebSessionMock();
        $this->mockSingleton(WebSession::class, $this->sessionMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->resetMockedSingletons();
    }

    protected function mockInputParams(array $params) : void
    {
        $this->inputMock->setParams($params);
    }

    protected function mockInputPost(array $post) : void
    {
        $this->inputMock->setPost($post);
    }

    protected function mockInputData(array $data) : void
    {
        $this->inputMock->setInput($data);
    }

    protected function mockInputMethod(string $method): void
    {
        $this->inputMock->setHttpMethod($method);
    }

    protected function mockSessionData(array $data) : void
    {
        $this->sessionMock->setData($data);
    }

    protected function getSession() : WebSessionMock
    {
        return $this->sessionMock;
    }

    /**
     * @throws \Lib\Exception
     */
    protected static function callAction(\Lib\IAction $action) : ?array
    {
        return $action->call();
    }

    /**
     * @throws \Lib\Exception
     */
    protected static function assertActionOutput(\Lib\IAction $action, ?array $expectedOutput) : void
    {
        self::assertEquals($expectedOutput, self::callAction($action));
    }
}