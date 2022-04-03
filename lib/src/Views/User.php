<?php

namespace Lib\Views;

use Lib\Models\User as Model;

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
            'skinUrl' => $this->getSkinUrl(),
            'privileges' => $this->getPrivileges(),
            'passwordResetRequired' => (bool) $this->model->password_reset,
        ];
    }

    protected function getSkinUrl(): ?string
    {
        $skin = $this->model->getSkin();
        if ($skin) {
            return static::URL_SKINS_PATH . $skin->getId()->format() . '.png';
        }

        return null;
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