<?php

namespace Lib\Actions;


class ServerJoined extends AbstractAction
{
    public function run(): ?array
    {
        $username = $this->getParam('username');
        $serverId = $this->getParam('serverId');
        if (!isset($username, $serverId)) {
            throw new \Lib\Exception('Missing required parameters', 400);
        }

        $users = \Lib\Models\User::find([
            'name' => $username,
            'auth_server_id' => $serverId,
        ], 1);
        if (empty($users)) {
            throw new \Lib\Exception('User not found or wrong serverId', 403);
        }

        $action = new GetProfile(reset($users));
        return $action->call();
    }
}
