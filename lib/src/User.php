<?php


namespace Lib;

/**
 * @property-read string $id
 * @property string $name
 * @property string $password_hash
 * @property string $mojang_uuid
 * @property string $skin
 * @property string $privilege_mask
 * @property string $created
 * @property string $last_login
 * @property string $auth_access_token
 * @property string $auth_server_id
 */
class User
{
    use TData;

    public const BIT_APPROVED = 0b1;
    public const BIT_ADMIN = 0b10;

    protected DB $db;
    protected bool $_new;

    protected function __construct(DB $db, array $data, bool $new)
    {
        $this->db = $db;
        $this->_new = $new;
        $this->initProperties($data);
    }

    public function readonly()
    {
        return ['id'];
    }

    public static function load(UUID $id) : self
    {
        $db = DB::instance();
        $data = $db->q('SELECT * FROM users WHERE id = ?', $id);
        if ($data === null) {
            throw new \Exception("User $id not found");
        }
        return new self($db, $data, false);
    }

    public static function create(array $data = []) : self
    {
        $db = DB::instance();
        if (empty($data['id'])) {
            $data['id'] = (string) (new UUID());
        }
        return new self($db, $data, true);
    }

    public function getId() : UUID
    {
        return new UUID($this->id);
    }

    public function getMojangUuid() : ?UUID
    {
        if ($this->mojang_uuid) {
            return new UUID($this->mojang_uuid);
        }
        return null;
    }

    public function addPrivileges(int $bits) : void
    {
        $mask = (int) $this->privilege_mask;
        $mask |= $bits;
        $this->privilege_mask = (string) $mask;
    }

    public function hasPrivileges(int $bits) : bool
    {
        $mask = (int) $this->privilege_mask;
        return (bool) ($mask & $bits);
    }

    public function isApproved() : bool
    {
        return $this->hasPrivileges(self::BIT_APPROVED);
    }

    public function isAdmin() : bool
    {
        return $this->hasPrivileges(self::BIT_ADMIN);
    }

    public function isNew() : bool
    {
        return $this->_new;
    }

    public function save() : void
    {
        $data = $this->getMutableProperties();
        if (empty($data)) {
            throw new \Exception('No data to save for user ' . $this->id . ($this->_new ? ' (new)' : ''));
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
                'INSERT INTO `users` (`id`, ' . implode(', ', $columns) . ') VALUES (:id, ' . implode(', ', $params) . ')',
                $data + ['id' => $this->id]
            );
            $this->_new = false;
        } else {
            $this->db->e(
                'UPDATE `users` SET (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $params) . ') WHERE `id` = :id',
                $data + ['id' => $this->id]
            );
        }
    }
}