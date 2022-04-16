<?php

namespace Lib\Views;

use Lib\Models\User as Model;
use Lib\Models\Skin;

class User extends AbstractView
{
    protected const URL_SKINS_PATH = '/assets/skins/';

    protected const PRIVILEGES_MAP = [
        'admin' => Model::BIT_ADMIN,
        'approved' => Model::BIT_APPROVED,
    ];

    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function render(): ?array
    {
        return [
            'id' => $this->model->getId()->format(),
            'name' => $this->model->name,
            'mojangUUID' => $this->model->mojang_uuid,
            'privileges' => $this->getPrivileges(),
            'passwordResetRequired' => (bool) $this->model->password_reset,
            'skins' => $this->getSkins(),
        ];
    }

    protected function getSkins(): array
    {
        $skins = Skin::find(['user_id' => $this->model->id], 0, ['updated' => Skin::SQL_DESC]);
        return array_map(fn($skin) => [
            'id' => $skin->getId()->format(),
            'url' => self::getSkinUrl($skin),
            'selected' => $skin->id === $this->model->skin_id,
        ], $skins);
    }

    public static function getSkinUrl(Skin $skin): string
    {
        $root = empty($_ENV['WEB_ROOT']) ? '' : ('/' . $_ENV['WEB_ROOT']);
        return $root . static::URL_SKINS_PATH . $skin->getId()->format() . '.png';
    }

    protected function getPrivileges(): array
    {
        $result = [];
        foreach (self::PRIVILEGES_MAP as $key => $bit) {
            $result[$key] = $this->model->hasPrivileges($bit);
        }
        return $result;
    }
}
