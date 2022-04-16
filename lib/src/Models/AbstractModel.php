<?php

namespace Lib\Models;

use Lib\DB;
use Lib\Exception;
use Lib\UUID;

/**
 * @property-read string $id
 */
abstract class AbstractModel
{
    public const SQL_ASC = 1;
    public const SQL_DESC = 2;
    protected const DIRECTION_MAP = [
        self::SQL_ASC => 'ASC',
        self::SQL_DESC => 'DESC',
    ];

    use \Lib\TData;

    protected DB $db;
    protected bool $_new;

    /**
     * @throws Exception
     */
    protected function __construct(DB $db, array $data, bool $new)
    {
        $this->db = $db;
        $this->_new = $new;
        $this->initProperties($data);
    }

    abstract public static function table(): string;

    public function readonly(): array
    {
        return ['id'];
    }

    /**
     * @throws Exception in case of db error or record not found
     * @return static
     */
    public static function load(UUID $id) : self
    {
        $db = DB::instance();
        $table = $db->name(static::table());
        $data = $db->q('SELECT * FROM `' . $table . '` WHERE id = ?', $id);
        if ($data === null) {
            throw new Exception("Record $table:$id not found", 404);
        }
        return new static($db, $data, false);
    }

    /**
     * @param array<UUID> $ids
     * @param bool $partial don't throw exception if some records are missing in the resulting array
     * @return array<static>
     * @throws Exception in case of db error or some of records not found
     */
    public static function loadAll(array $ids, bool $partial = false): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = DB::instance();
        $table = $db->name(static::table());
        $keys = [];
        $params = [];
        foreach (array_values($ids) as $i => $id) {
            $key = 'id' . $i++;
            $keys[] = $key;
            $params[$key] = $id;
        }
        $rows = $db->qAll('SELECT * FROM `' . $table . '` WHERE id IN (' . implode(',', $keys) . ')', $params);
        if (!$partial && count($rows) !== count($ids)) {
            $notFound = array_flip(array_map('strval', $ids));
            foreach ($rows as $row) {
                unset($notFound[$row['id']]);
            }
            throw new Exception("Some `$table` records not found: " . implode(', ', array_keys($notFound)), 404);
        }
        $models = [];
        foreach ($rows as $row) {
            $models[] = new static($db, $row, false);
        }
        return $models;
    }

    /**
     * @param array<string,mixed> $constraints
     * @param int $limit
     * @param array<string,int> $order
     * @return static[]
     * @throws Exception
     */
    public static function find(array $constraints, int $limit = 0, array $order = []): array
    {
        static $ops = ['<', '>', '=', '>=', '<=', '<>'];
        $db = DB::instance();
        $table = $db->name(static::table());
        $query = 'SELECT * FROM `' . $table . '`';
        $params = [];
        $i = 0;
        if ($constraints) {
            foreach ($constraints as $column => $constraint) {
                $column = $db->name($column);
                if (is_array($constraint)) {
                    if (count($constraint) === 2 && is_scalar($constraint[0])) {
                        $constraint = [$constraint];
                    }
                    foreach ($constraint as $c) {
                        if (!is_array($c) || count($c) !== 2) {
                            throw new Exception("Invalid constraint `$column`: " . print_r($c, true));
                        }
                        [$op, $val] = $c;
                        if (!in_array($op, $ops, true)) {
                            throw new Exception("Invalid operation supplied for constraint `$column` --> $op <-- '$val'");
                        }
                        $query .= $i === 0 ? ' WHERE ' : ' AND ';
                        $key = 'param' . ($i++);
                        $query .= "`$column` $op :$key";
                        $params[$key] = $val;
                    }
                } else {
                    $query .= $i === 0 ? ' WHERE ' : ' AND ';
                    $key = 'param' . ($i++);
                    $query .= "`$column` = :$key";
                    $params[$key] = $constraint;
                }
            }
        }
        if ($order) {
            $query .= ' ORDER BY ';
            foreach ($order as $column => $direction) {
                $column = $db->name($column);
                if (!isset(self::DIRECTION_MAP[$direction])) {
                    throw new Exception("Invalid direction for column `$column`: " . var_export($direction, true));
                }
                $direction = self::DIRECTION_MAP[$direction];
                $query .= "`$column` $direction";
            }
        }
        if ($limit > 0) {
            $query .= " LIMIT $limit";
        }
        $rows = $db->qAll($query, $params);
        $models = [];
        foreach ($rows as $row) {
            $models[] = new static($db, $row, false);
        }
        return $models;
    }

    /** @return static */
    public static function create(array $data = []): self
    {
        $db = DB::instance();
        if (empty($data['id'])) {
            $data['id'] = (string) (new UUID());
        }
        return new static($db, $data, true);
    }

    public function getId(): UUID
    {
        return new UUID($this->id);
    }

    public function isNew(): bool
    {
        return $this->_new;
    }

    /**
     * INSERTS or UPDATES table row
     * @throws Exception
     */
    public function save(): void
    {
        if ($this->_new) {
            $data = $this->getAllProperties();
        } else {
            $data = $this->getMutableProperties();
        }
        $table = $this->db->name(static::table());
        if (empty($data)) {
            throw new Exception('No data to save for ' . $table . ':' . $this->id . ($this->_new ? ' (new)' : ''));
        }
        $keys = array_map(function($key) {
            return $this->db->name($key);
        }, array_keys($data));
        $columns = array_map(function($key) {
            return "`$key`";
        }, $keys);
        $params = array_map(function($key) {
            return ":$key";
        }, $keys);

        if ($this->_new) {
            $this->db->e(
                'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $params) . ')',
                $data
            );
            $this->_new = false;
        } else {
            $updateStr = implode(', ', array_map(function($col, $param) {
                return "$col = $param";
            }, $columns, $params));
            $this->db->e(
                'UPDATE `' . $table . '` SET ' . $updateStr . ' WHERE `id` = :id',
                $data + ['id' => $this->id]
            );
        }
    }
}
