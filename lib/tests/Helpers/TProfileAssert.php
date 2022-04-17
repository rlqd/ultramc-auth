<?php

namespace Tests\Helpers;

/**
 * @mixin ActionTestCase
 */
trait TProfileAssert
{
    /**
     * @throws \Lib\Exception
     */
    public static function assertProfileAction(
        \Lib\IAction $action,
        \Lib\UUID $userId,
        string $name,
        ?\Lib\UUID $skinId,
        string $skinUpdated,
        string $signature
    ) : void
    {
        self::assertActionOutput($action, [
            'id' => $userId->format(),
            'name' => $name,
            'properties' => [
                [
                    'name' => 'textures',
                    'value' => base64_encode(\Lib\Controller::instance()->encode([
                        'timestamp' => strtotime($skinUpdated),
                        'profileId' => $userId->format(),
                        'profileName' => $name,
                        'textures' => $skinId ? [
                            'SKIN' => [
                                'url' => \Lib\Actions\GetProfile::MOJANG_TEXTURES_URL . $skinId->format()
                            ],
                        ] : [],
                    ])),
                    'signature' => $signature,
                ],
            ],
        ]);
    }
}