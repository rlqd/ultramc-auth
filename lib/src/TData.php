<?php


namespace Lib;

/**
 * @method string[] readonly
 */
trait TData
{
    private array $_data = [];

    protected function initProperties(array $data)
    {
        if (!empty($this->_data)) {
            throw new Exception('Properties were already inited for ' . static::class);
        }
        $this->_data = array_map('strval', $data);
    }

    public function getAllProperties() : array
    {
        return $this->_data;
    }

    public function getMutableProperties() : array
    {
        $readonly = method_exists($this, 'readonly') ? $this->readonly() : [];
        return array_diff_key($this->_data, array_flip($readonly));
    }

    /**
     * @param $name
     * @return string|null
     */
    public function __get($name)
    {
        return $this->_data[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $readonly = method_exists($this, 'readonly') ? $this->readonly() : [];
        if (in_array($name, $readonly, true)) {
            throw new Exception('Trying to set readonly property ' . static::class . '::' . $name);
        }
        if ($value === null) {
            unset($this->_data[$name]);
        } else {
            $this->_data[$name] = (string) $value;
        }
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }
}