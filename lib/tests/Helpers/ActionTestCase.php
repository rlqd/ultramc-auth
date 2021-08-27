<?php

namespace Tests\Helpers;


class ActionTestCase extends DbTestCase
{
    private InputMock $inputMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inputMock = new InputMock();
        $this->mockSingleton(\Lib\Input::class, $this->inputMock);
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

    protected function mockInputData(array $data) : void
    {
        $this->inputMock->setInput($data);
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