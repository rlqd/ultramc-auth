<?php

namespace Tests\Unit;


class ControllerTest extends \PHPUnit\Framework\TestCase
{
    public function providerRun() : array
    {
        return [
            [
                'actionOutput' => ['hello' => 'world'],
                'expectedResult' => '{"hello":"world"}',
            ],
            [
                'actionOutput' => null,
                'expectedResult' => '',
            ],
        ];
    }

    /**
     * @dataProvider providerRun
     * @param array|null $actionOutput
     * @param string $expectedResult
     */
    public function testRun(?array $actionOutput, string $expectedResult) : void
    {
        $action = new \Tests\Helpers\EchoAction($actionOutput);
        ob_start();
        \Lib\Controller::instance()->run($action);
        $output = ob_get_clean();
        self::assertEquals($expectedResult, $output);
    }
}