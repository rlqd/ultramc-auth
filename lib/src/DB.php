<?php


namespace Lib;


class DB
{
    use TSingleton;

    protected \PDO $pdo;

    protected function __construct()
    {
        $this->pdo = new \PDO($_ENV['DB_DSL'], $_ENV['DB_USER'] ?? null, $_ENV['DB_PASS'] ?? null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
    }

    public function name(string $col) : string
    {
        if (!preg_match('/^\w+$/', $col)) {
            throw new Exception('Invalid column name: ' . $col);
        }
        return $col;
    }

    protected function queryInternal($query, $params, $select, $style = null, $all = null)
    {
        try {
            if ($params !== null) {
                if (!is_array($params)) {
                    $params = [$params];
                }
                $st = $this->pdo->prepare($query);
                if ($st && $st->execute($params)) {
                    if ($select) {
                        $data = $all ? $st->fetchAll($style) : $st->fetch($style);
                        $st->closeCursor();
                        if ($data === false) {
                            $data = null;
                        }
                        return $data;
                    } else {
                        return $st->rowCount();
                    }
                }
            } else {
                if ($select) {
                    $st = $this->pdo->query($query);
                    if ($st) {
                        $data = $all ? $st->fetchAll($style) : $st->fetch($style);
                        $st->closeCursor();
                        if ($data === false) {
                            $data = null;
                        }
                        return $data;
                    }
                } else {
                    $result = $this->pdo->exec($query);
                    if ($result !== false) {
                        return $result;
                    }
                }
            }
        } catch (\PDOException $e) {
            throw new Exception("Failed to execute DB query: $query", 500, $e);
        }
        throw new Exception("Failed to execute DB query: $query\nPDO code: " . $this->pdo->errorCode()
            . "\nPDO error: " . print_r($this->pdo->errorInfo(), true));
    }

    /**
     * @param $query
     * @param null $params
     * @param int $style
     * @return array|null
     * @throws Exception
     */
    public function q($query, $params = null, $style = \PDO::FETCH_ASSOC)
    {
        return $this->queryInternal($query, $params, true, $style, false);
    }

    /**
     * @param $query
     * @param null $params
     * @param null $default
     * @return mixed|null
     * @throws Exception
     */
    public function qVal($query, $params = null, $default = null)
    {
        $data = $this->q($query, $params, \PDO::FETCH_NUM);
        if (empty($data)) {
            return $default;
        }
        return $data[0];
    }

    /**
     * @param $query
     * @param null $params
     * @param int $style
     * @return array
     * @throws Exception
     */
    public function qAll($query, $params = null, $style = \PDO::FETCH_ASSOC) : array
    {
        return $this->queryInternal($query, $params, true, $style, true);
    }

    /**
     * @param $query
     * @param null $params
     * @return int
     * @throws Exception
     */
    public function e($query, $params = null) : int
    {
        return (int) $this->queryInternal($query, $params, false);
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->pdo->lastInsertId();
    }
}