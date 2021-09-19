<?php

namespace Lib\Actions;


class ClientJoin extends AbstractAction
{
    public function run(): ?array
    {
        $data = $this->getInput();
        if (!isset($data['selectedProfile'], $data['accessToken'], $data['serverId'])) {
            throw new \Lib\Exception('Missing required data', 400);
        }

        $uuid = new \Lib\UUID($data['selectedProfile']);
        $user = $this->loadActiveUser($uuid);

        $sessId = new \Lib\UUID($data['accessToken']);
        $user->getSession($sessId)->touch();

        $user->auth_server_id = $data['serverId'];
        $user->save();

        return null;
    }
}