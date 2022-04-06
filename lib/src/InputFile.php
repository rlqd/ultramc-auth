<?php

namespace Lib;

/**
 * @property-read string $name
 * @property-read string $type
 * @property-read int $size
 * @property-read string $tmp_name
 * @property-read int $error
 * @property-read string|null $full_path
 */
class InputFile extends LazyKeyIterator
{
    protected const FIELDS = [
        'name',
        'type',
        'size',
        'tmp_name',
        'error',
        'full_path',
    ];

    protected array $info;

    public function __construct(array $info)
    {
        $keys = is_array($info['error']) ? array_keys($info['error']) : [];
        parent::__construct($keys);
        $this->info = $info;
    }

    protected function createValue($key)
    {
        $subInfo = [];
        foreach (self::FIELDS as $field) {
            if (isset($this->info[$field][$key])) {
                $subInfo[$field] = $this->info[$field][$key];
            }
        }
        return new static($subInfo);
    }

    public function __get($property)
    {
        if (!in_array($property, self::FIELDS, true)) {
            throw new Exception('Unknown property for files input: ' . $property);
        }
        if ($this->isArray()) {
            throw new Exception("Trying to access property '$property' on file array");
        }
        return $this->info[$property] ?? null;
    }

    public function isArray(): bool
    {
        return count($this) > 0;
    }

    public function isSuccess(): bool
    {
        return $this->error === \UPLOAD_ERR_OK;
    }

    public function save(string $destination): void
    {
        if (!$this->isSuccess()) {
            throw new Exception('File upload was not successfull: error code ' . $this->error);
        }
        if (!$this->moveFile($destination)) {
            throw new Exception('Failed to move uploaded file to destination: ' . $destination);
        }
    }

    protected function moveFile(string $destination): bool
    {
        return move_uploaded_file($this->tmp_name, $destination);
    }
}