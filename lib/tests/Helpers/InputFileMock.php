<?php

namespace Tests\Helpers;

class InputFileMock extends \Lib\InputFile
{
    private $inputMock;

    public function __construct(InputMock $inputMock, array $info)
    {
        parent::__construct($info);
        $this->inputMock = $inputMock;
    }

    protected function moveFile(string $destination): bool
    {
        $this->inputMock->logFileUpload($this->tmp_name, $destination);
        return $this->info['save_result_mock'] ?? true;
    }
}
