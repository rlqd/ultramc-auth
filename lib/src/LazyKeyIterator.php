<?php

namespace Lib;

abstract class LazyKeyIterator implements \ArrayAccess, \Iterator, \Countable
{
    protected array $keys;
    protected array $values = [];
    protected int $index = 0;

    public function __construct(array $keys)
    {
        $this->keys = array_values($keys);
    }

    abstract protected function createValue($key);

    public function offsetExists($offset): bool
    {
        return in_array($offset, $this->keys, true);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }
        if (!isset($this->values[$offset])) {
            $this->values[$offset] = $this->createValue($offset);
        }
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new Exception('Can not set value for lazy iterator');
    }

    public function offsetUnset($offset): void
    {
        throw new Exception('Can not unset value for lazy iterator');
    }

    public function current()
    {
        return $this->offsetGet($this->key());
    }

    public function key()
    {
        return $this->keys[$this->index] ?? null;
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function valid(): bool
    {
        return $this->index < $this->count();
    }

    public function count(): int
    {
        return count($this->keys);
    }
}
