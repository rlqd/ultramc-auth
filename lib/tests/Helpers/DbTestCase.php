<?php


namespace Tests\Helpers;


use Lib\DB;
use PHPUnit\Framework\TestCase;

abstract class DbTestCase extends TestCase
{
    public const OP_SELECT = 'SELECT';
    public const OP_INSERT = 'INSERT';
    public const OP_UPDATE = 'UPDATE';
    public const OP_DELETE = 'DELETE';

    private $DbMock;
    private $DbInstanceProp;
    /** @var array<DbQueryMock> */
    private $expectedQueries = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->DbMock = new DbMock();
        $this->DbInstanceProp = new \ReflectionProperty(DB::class, 'instance');
        $this->DbInstanceProp->setAccessible(true);
        $this->DbInstanceProp->setValue($this->DbMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if ($this->DbInstanceProp) {
            $this->DbInstanceProp->setValue(null);
        }
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
            $this->DbMock->addResultMock($q->statement, $q->table, $q->result);
            if ($q->expected) {
                $this->expectedQueries[] = $q;
            }
        }
    }

    protected function assertExpectedQueries() : void
    {
        if (empty($this->expectedQueries)) {
            return;
        }
        $expectedQueries = $this->expectedQueries;
        $runQueries = $this->DbMock->getQueries();
        $i = 0;
        foreach ($runQueries as [$definition, $sql, $params]) {
            $expected = reset($expectedQueries);
            if ($expected === false) {
                break;
            }
            if ($expected->getDefinition() === $definition) {
                if ($expected->expectedSql !== null) {
                    self::assertEquals($expected->expectedSql, $sql, "Got wrong SQL for query #$i");
                }
                if ($expected->expectedParams !== null) {
                    self::assertEquals($expected->expectedParams, $params, "Got wrong params for query #$i");
                }
                array_shift($expectedQueries);
                ++$i;
            } else {
                if ($expected->select) {
                    self::fail("Wrong query #$i (expected " . $expected->getDefinition() . ", got $definition)");
                }
            }
        }
        if (!empty($expectedQueries)) {
            self::fail("Expected query #$i (" . reset($expectedQueries)->getDefinition() . ") not executed");
        }
    }
}