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
            throw new \Exception('Properties were already inited for ' . static::class);
        }
        $this->_data = $data;
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

    /**
     * @param string $name
     * @param string $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        $readonly = method_exists($this, 'readonly') ? $this->readonly() : [];
        if (in_array($name, $readonly)) {
            throw new \Exception('Trying to set readonly property ' . static::class . '::' . $name);
        }
        $this->_data[$name] = $value;
    }
}