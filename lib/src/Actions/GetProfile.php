<?php

namespace Lib\Actions;

/**
 * GET:
 * @property-read string $uuid
 */
class GetProfile extends AbstractAction
{
    public const MOJANG_TEXTURES_URL = 'http://textures.minecraft.net/texture/';
    public const DEFAULT_TS = '1970-01-02 00:00:00';

    protected ?\Lib\Models\User $user = null;

    public function __construct(\Lib\Models\User $user = null)
    {
        $this->user = $user;
    }

    public function run(): ?array
    {
        if ($this->user === null) {
            if (empty($this->uuid)) {
                throw new \Lib\Exception('Missing required parameter uuid', 400);
            }
            $userId = new \Lib\UUID($this->uuid);
            $this->user = \Lib\Models\User::load($userId);
        }

        return [
            'id' => $this->user->getId()->format(),
            'name' => $this->user->name,
            'properties' => [
                $this->signProperty('textures', $this->renderTextures())
            ],
        ];
    }

    /**
     * @throws \Lib\Exception
     */
    protected function signProperty(string $name, array $value) : array
    {
        $encoded = \Lib\Controller::instance()->encode($value);

        $keyFile = DATA_DIR . '/yggdrasil_session_private.pem';
        if (!is_file($keyFile)) {
            throw new \Lib\Exception('Key file not found: ' . $keyFile);
        }
        $key = openssl_pkey_get_private('file://' . $keyFile);
        if ($key === false) {
            throw new \Lib\Exception('Failed to load key file: ' . $keyFile);
        }
        if (!openssl_sign($encoded, $signature, $key, 'sha1WithRSAEncryption')) {
            throw new \Lib\Exception('Failed to sign property');
        }

        return [
            'name' => $name,
            'value' => $encoded,
            'signature' => base64_encode($signature),
        ];
    }

    /**
     * @throws \Lib\Exception
     */
    protected function renderTextures() : array
    {
        $skin = $this->user->getSkin();
        return [
            'timestamp' => $skin ? strtotime($skin->updated) : strtotime(self::DEFAULT_TS),
            'profileId' => $this->user->getId()->format(),
            'profileName' => $this->user->name,
            'textures' => $skin === null ? [] : [
                'SKIN' => [
                    'url' => self::MOJANG_TEXTURES_URL . $skin->getId()
                ],
            ],
        ];
    }
}