<?php

namespace Tests\Unit;

use Lib\InputFile;
use Tests\Helpers\InputMock;
use Tests\Helpers\InputFileMock;

class InputFileTest extends \PHPUnit\Framework\TestCase
{
    public function testSingle(): void
    {
        $file = new InputFile([
            'name' => 'test.png',
            'type' => 'image/png',
            'size' => 1024,
            'tmp_name' => '/tmp/12345',
            'error' => \UPLOAD_ERR_OK,
        ]);
        self::assertCount(0, $file);
        self::assertEquals('test.png', $file->name);
        self::assertTrue($file->isSuccess());
    }

    public function providerMultiple(): array
    {
        return [
            [
                'indices' => [0, 1],
                'notExisting' => 3,
            ],
            [
                'incides' => ['a', 'b'],
                'notExisting' => 'z',
            ],
        ];
    }

    /**
     * @dataProvider providerMultiple
     */
    public function testMultiple(array $indices, $notExisting): void
    {
        [$a, $b] = $indices;
        $file = new InputFile([
            'name' => [$a => 'test1.png', $b => 'test2.png'],
            'type' => array_fill_keys($indices, 'image/png'),
            'size' => array_fill_keys($indices, 1024),
            'tmp_name' => array_fill_keys($indices, '/tmp/12345'),
            'error' => [$a => \UPLOAD_ERR_OK, $b => \UPLOAD_ERR_NO_FILE],
        ]);

        self::assertCount(2, $file);
        self::assertTrue(isset($file[$a]));
        self::assertFalse(isset($file[$notExisting]));

        self::assertEquals('test1.png', $file[$a]->name);
        self::assertEquals('test2.png', $file[$b]->name);
        self::assertTrue($file[$a]->isSuccess());
        self::assertFalse($file[$b]->isSuccess());

        $iterated = [];
        foreach ($file as $key => $subFile) {
            self::assertEquals(current($indices), $key);
            $iterated[] = $subFile;
            next($indices);
        }
        self::assertCount(2, $iterated);
        self::assertEquals($file[$a]->name, $iterated[0]->name);
        self::assertEquals($file[$b]->name, $iterated[1]->name);

        $this->expectException(\Lib\Exception::class);
        $this->expectExceptionMessage('Trying to access property \'name\' on file array');
        echo $file->name;
    }

    public function testSaveSuccess(): void
    {
        $input = new InputMock();
        $file = new InputFileMock($input, [
            'name' => 'test.png',
            'type' => 'image/png',
            'size' => 1024,
            'tmp_name' => '/tmp/12345',
            'error' => \UPLOAD_ERR_OK,
        ]);
        $file->save('assets/skin.png');
        self::assertEquals(['/tmp/12345' => 'assets/skin.png'], $input->getSavedFiles());
    }

    public function providerSaveFailure(): array
    {
        return [
            [
                'file' => [
                    'name' => 'test.png',
                    'type' => 'image/png',
                    'size' => 1024,
                    'tmp_name' => '/tmp/12345',
                    'error' => \UPLOAD_ERR_NO_FILE,
                ],
                'destination' => 'assets/skin.png',
                'expectedError' => 'File upload was not successfull: error code ' . \UPLOAD_ERR_NO_FILE,
            ],
            [
                'file' => [
                    'name' => 'test.png',
                    'type' => 'image/png',
                    'size' => 1024,
                    'tmp_name' => '/tmp/12345',
                    'error' => \UPLOAD_ERR_OK,
                    'save_result_mock' => false,
                ],
                'destination' => 'assets/skin.png',
                'expectedError' => 'Failed to move uploaded file to destination: assets/skin.png',
            ],
        ];
    }

    /**
     * @dataProvider providerSaveFailure
     */
    public function testSaveFailure(array $file, string $destination, string $expectedError): void
    {
        $input = new InputMock();
        $file = new InputFileMock($input, $file);

        $this->expectException(\Lib\Exception::class);
        $this->expectExceptionMessage($expectedError);
        $file->save($destination);
    }
}
