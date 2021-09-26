<?php

namespace Tests\Unit\Actions;


use Lib\UUID;

class GetProfileTest extends \Tests\Helpers\ActionTestCase
{
    use \Tests\Helpers\TProfileAssert;

    public function testWithSkin() : void
    {
        $userId = new UUID('4e8b178de057410ca5ee9777339508c3');
        $skinId = new UUID('1ec0368fe2c96720fbe664cdf66f90d2');
        $updated = '2021-01-01 00:00:00';
        $this->mockInputParams(['uuid' => $userId->format()]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->result([
                    'id' => (string) $userId,
                    'name' => 'rlqd',
                    'skin_id' => (string) $skinId,
                ]),
            $this->query(self::OP_SELECT, 'skins')
                ->expect(null, [$skinId])
                ->result([
                    'id' => (string) $skinId,
                    'user_id' => (string) $userId,
                    'updated' => $updated,
                ]),
        );
        $action = new \Lib\Actions\GetProfile();
        self::assertProfileAction(
            $action,
            $userId,
            'rlqd',
            $skinId,
            $updated,
            'vTrAy3dXa7Co0UkAEYW8u5OP9uQ1vSJbEFSLconsCPLhgabmAjvZD07NsiPedoMrgPlFW73EA7gCtgm0GL1U1ZFxxNzrkVRZtkQF0ZcOZ+jX23/Xxgz9lBkuMIpHwBznVfBd1r2wgrI0D065yDDr/hy7KHeXjfZCqVEGqSubkgU='
        );
    }

    public function testWithoutSkin() : void
    {
        $userId = new UUID('4e8b178de057410ca5ee9777339508c3');
        $this->mockInputParams(['uuid' => $userId->format()]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->result([
                    'id' => (string) $userId,
                    'name' => 'rlqd',
                ]),
        );
        $action = new \Lib\Actions\GetProfile();
        self::assertProfileAction(
            $action,
            $userId,
            'rlqd',
            null,
            \Lib\Actions\GetProfile::DEFAULT_TS,
            'RJpPx5YVjUlLYK0q/bEzzyBWpve2U+nCqWgxxLMg7kVoKFnNTyZ7bDvxbS2yw8S27lcuyQcdzsLZufcvFg/SxnzH4Gq+8N+VWhUtvyhuJIoW5Y0GdLsv4PJnv9ckwfKUPiESxwzXVnp+DPRNppy8Ns4A0GLOIY0U51mpkpWHjfI='
        );
    }

    public function testMojangLinked() : void
    {
        $userId = new UUID('4e8b178de057410ca5ee9777339508c3');
        $mojangId = new UUID('1ec1e61d4e9b6f828987f7392520cbc7');
        $this->mockInputParams(['uuid' => $userId->format()]);
        $this->mockQueries(
            $this->query(self::OP_SELECT, 'users')
                ->result([
                    'id' => (string) $userId,
                    'name' => 'rlqd',
                    'mojang_uuid' => (string) $mojangId,
                ]),
        );
        $action = new \Lib\Actions\GetProfile();
        self::assertProfileAction(
            $action,
            $mojangId,
            'rlqd',
            null,
            \Lib\Actions\GetProfile::DEFAULT_TS,
            'sa5nwuASg9V5nZh0Nb/Q3qKv2w4ec6Za1RD0tUQGqZoehCKftF4/oSnZkpQbNkiH3oR/ur3E5uuHOp/3DRJkn6W8KyQscC9cBCm63+jlwJeDdBRPfRnxQRupLigIXAtDv+FKl05bwPjJgE4+jCzFndKToMKXVVCcFu8z6PLLzBk='
        );
    }
}