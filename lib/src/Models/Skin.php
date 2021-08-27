<?php

namespace Lib\Models;

/**
 * @property-read string $user_id
 * @property string $updated
 */
class Skin extends AbstractModel
{
    public static function table(): string
    {
        return 'skins';
    }

    public function readonly(): array
    {
        $readonly = parent::readonly();
        $readonly[] = 'user_id';
        return $readonly;
    }

    public function getUserId() : \Lib\UUID
    {
        return new \Lib\UUID($this->user_id);
    }

    /**
     * Refresh skin updated datetime
     * @throws \Lib\Exception
     */
    public function touch() : void
    {
        $this->updated = new \Lib\DateTime();
        $this->save();
    }
}