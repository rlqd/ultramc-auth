<?php

namespace Tests\Helpers;


class DbQueryMock
{
    public string $statement;
    public string $table;
    public bool $select = false;
    public $result = DbMock::DEFAULT_RESULT;
    public bool $expected = false;
    public ?string $expectedSql = null;
    public ?array $expectedParams = null;
    public ?string $assertionFailure = null;

    public function __construct(string $statement, string $table)
    {
        $this->statement = $statement;
        $this->table = $table;
    }

    public function getDefinition() : string
    {
        return $this->statement . ':' . $this->table;
    }

    public function result($result) : self
    {
        $this->select = true;
        $this->result = $result;
        return $this;
    }

    public function expect(string $sql = null, array $params = null) : self
    {
        $this->expected = true;
        $this->expectedSql = $sql;
        $this->expectedParams = $params;
        return $this;
    }

    public function setFailure(string $message) : void
    {
        if ($this->assertionFailure === null) {
            $this->assertionFailure = $message;
        }
    }

    public function getFailure() : string
    {
        return $this->assertionFailure ?? 'Query was not executed';
    }
}
