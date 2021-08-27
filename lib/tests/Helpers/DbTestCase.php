<?php


namespace Tests\Helpers;


use Lib\DB;
use PHPUnit\Framework\TestCase;

abstract class DbTestCase extends TestCase
{
    use TSingletonMock;

    public const OP_SELECT = 'SELECT';
    public const OP_INSERT = 'INSERT';
    public const OP_UPDATE = 'UPDATE';
    public const OP_DELETE = 'DELETE';

    private $dbMock;
    /** @var array<DbQueryMock> */
    private $expectedQueries = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbMock = new DbMock();
        $this->mockSingleton(DB::class, $this->dbMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->resetMockedSingletons();
    }

    protected function runTest()
    {
        $result = parent::runTest();
        $this->assertExpectedQueries();
        return $result;
    }

    protected function query(string $op, string $table) : DbQueryMock
    {
        return new DbQueryMock($op, $table);
    }

    protected function mockQueries(DbQueryMock ...$query) : void
    {
        foreach ($query as $i => $q) {
            if ($q->statement === self::OP_SELECT && !$q->select) {
                self::fail("Trying to mock SELECT query #$i without a result");
            }
            $this->dbMock->addResultMock($q->statement, $q->table, $q->result);
            if ($q->expected) {
                $this->expectedQueries[$i] = $q;
            }
        }
    }

    protected function assertExpectedQueries() : void
    {
        if (empty($this->expectedQueries)) {
            return;
        }
        $expectedQueries = $this->expectedQueries;
        $runQueries = $this->dbMock->getQueries();
        foreach ($runQueries as [$definition, $sql, $params]) {
            $expected = reset($expectedQueries);
            if ($expected === false) {
                break;
            }
            $i = array_key_first($expectedQueries);
            if ($expected->getDefinition() !== $definition) {
                continue;
            }
            if ($expected->expectedSql !== null && mb_strpos($sql, $expected->expectedSql) === false) {
                $expected->setFailure("Got wrong SQL: $sql");
                continue;
            }
            if ($expected->expectedParams !== null) {
                foreach ($expected->expectedParams as $key => $value) {
                    if (!isset($params[$key])) {
                        $expected->setFailure("Missing param '$key' (all params: " . implode(', ', array_keys($params)) . ")");
                        continue 2;
                    }
                    if (((string)$params[$key]) !== ((string)$value)) {
                        $expected->setFailure("Wrong param value $key: " . $params[$key] . " (expected: $value)");
                        continue 2;
                    }
                }
            }
            unset($expectedQueries[$i]);
        }
        if (!empty($expectedQueries)) {
            $i = array_key_first($expectedQueries);
            $expected = reset($expectedQueries);
            self::fail("Failed to assert query #$i (" . $expected->getDefinition() . "): " . $expected->getFailure());
        }
    }
}