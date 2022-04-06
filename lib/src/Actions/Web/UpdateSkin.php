<?php

namespace Lib\Actions\Web;

use Lib\DB;
use Lib\Exception;
use \Lib\UUID;
use \Lib\Models\Skin;
use \Lib\Views\User as UserView;

/**
 * POST:
 * @property-read string $skinId
 * @property-read \Lib\InputFile $skin
 */
class UpdateSkin extends AbstractAction
{
    protected function run(): ?array
    {
        if (isset($this->skinId)) {
            $skin = Skin::load(new UUID($this->skinId));
            if ($skin->user_id !== $this->currentUser->id) {
                throw new Exception('This skin belongs to another user', Exception::FORBIDDEN);
            }
        } elseif ($this->skin instanceof \Lib\InputFile) {
            $skinId = new UUID();
            $this->skin->save(ASSETS_DIR . '/skins/' . $skinId->format() . '.png');
            $skin = Skin::create([
                'id' => $skinId,
                'user_id' => $this->currentUser->id,
            ]);
        }

        DB::instance()->inTransaction(function() use ($skin) {
            if ($skin->isNew()) {
                $skin->save();
            } else {
                $skin->touch();
            }
            $this->currentUser->skin_id = $skin->id;
            $this->currentUser->save();
        });

        return [
            'success' => true,
            'skin' => [
                'id' => $skin->getId()->format(),
                'url' => UserView::getSkinUrl($skin),
                'selected' => true,
            ],
        ];
    }
}
