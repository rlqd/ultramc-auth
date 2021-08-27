<?php


namespace Tests\Helpers;


use Lib\DB;
use PHPUnit\Framework\AssertionFailedError;

class DbMock extends DB
{
    public const DEFAULT_RESULT = 0;

    private const REGEX_SQL_STATEMENT = '/\s*([A-Z]+)\s+/';
    private const REGEX_SQL_TABLE = '/(?:INSERT\s+INTO|INSERT\s+IGNORE\s+INTO|SELECT\s+.+?\s+FROM|UPDATE|DELETE\s+FROM)\s+`?(\w+)`?\s+/';

    protected $queries = [];
    protected $mocks = [];

    public function __construct() {}

    protected function killTest(string $message)
    {
        throw new AssertionFailedError($message);
    }

    protected function queryInternal($query, $params, $select, $style = null, $all = null)
    {
        if ($params !== null && !is_array($params)) {
            $params = [$params];
        }
        preg_match(self::REGEX_SQL_STATEMENT, $query, $matches);
        $statement = $matches[1];
        if (preg_match(self::REGEX_SQL_TABLE, $query, $matches)) {
            $definition = $statement . ':' . $matches[1];
        } else {
            if ($select) {
                $this->killTest('Not found table name for query: ' . $query);
            }
            $definition = $statement;
        }
        $this->queries[] = [$definition, $query, $params];
        if (empty($this->mocks[$definition])) {
            if ($select) {
                $this->killTest('Query result mock not found for ' . $definition);
            }
            return self::DEFAULT_RESULT;
        }
        return array_shift($this->mocks[$definition]);
    }

    public function id()
    {
        static $id = 0;
        return ++$id;
    }

    public function addResultMock(string $statement, string $table, $result)
    {
        $definition = $statement . ':' . $table;
        $this->mocks[$definition][] = $result;
    }

    public function getQueries() : array
    {
        return $this->queries;
    }
}