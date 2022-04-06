<?php

namespace Tests\Unit\Actions\Web;

use Lib\UUID;
use Lib\UUIDFactory;
use Lib\Actions\Web\UpdateSkin;
use Tests\Helpers\UUIDFactoryMock;
use Tests\Helpers\ActionTestCase;

class UpdateSkinTest extends ActionTestCase
{
    public function testSelect(): void
    {
        $userId = new UUID();
        $skinId = new UUID();
        $this->mockInputPost([
            'skinId' => $skinId->format(),
        ]);
        $this->mockSessionData([
            'user_id' => $userId,
        ]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect(null, [$userId])
                ->result(
                    [
                        'id' => (string) $userId,
                        'name' => 'rlqd',
                        'skin_id' => (string) $skinId,
                        'privilege_mask' => 0,
                        'password_reset' => 0,
                    ]
                ),
            $this->query(self::OP_SELECT, 'skins')
                ->expect(null, [$skinId])
                ->result(
                    [
                        'id' => (string) $skinId,
                        'user_id' => (string) $userId,
                    ]
                ),
            $this->query(self::OP_UPDATE, 'skins')
                ->expect()
                ->inTransaction()
                ->result(1),
            $this->query(self::OP_UPDATE, 'users')
                ->expect(null, ['id' => $userId, 'skin_id' => $skinId])
                ->inTransaction()
                ->result(1),
        );

        self::assertActionOutput(new UpdateSkin(), [
            'success' => true,
            'skin' => [
                'id' => $skinId->format(),
                'url' => '/assets/skins/' . $skinId->format() . '.png',
                'selected' => true,
            ],
        ]);
    }

    public function testUpload(): void
    {
        $userId = new UUID();
        $skinId = new UUID();
        $this->mockInputFiles([
            'skin' => [
                'name' => 'steve.png',
                'type' => 'image/png',
                'size' => 1024,
                'tmp_name' => '/tmp/12345',
                'error' => \UPLOAD_ERR_OK,
            ],
        ]);
        $this->mockSessionData([
            'user_id' => $userId,
        ]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->expect(null, [$userId])
                ->result(
                    [
                        'id' => (string) $userId,
                        'name' => 'rlqd',
                        'skin_id' => (string) $skinId,
                        'privilege_mask' => 0,
                        'password_reset' => 0,
                    ]
                ),
            $this->query(self::OP_INSERT, 'skins')
                ->expect()
                ->inTransaction()
                ->result(1),
            $this->query(self::OP_UPDATE, 'users')
                ->expect(null, ['id' => $userId, 'skin_id' => $skinId])
                ->inTransaction()
                ->result(1),
        );
        $this->mockSingleton(UUIDFactory::class, new UUIDFactoryMock([$skinId]));

        self::assertActionOutput(new UpdateSkin(), [
            'success' => true,
            'skin' => [
                'id' => $skinId->format(),
                'url' => '/assets/skins/' . $skinId->format() . '.png',
                'selected' => true,
            ],
        ]);
        $this->assertSavedFiles([
            '/tmp/12345' => ASSETS_DIR . '/skins/' . $skinId->format() . '.png',
        ]);
    }
}