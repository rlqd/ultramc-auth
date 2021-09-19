<?php

namespace Lib\Actions;

/**
 * GET:
 * @property-read string $username
 * @property-read string $serverId
 */
class ServerJoined extends AbstractAction
{
    public function run(): ?array
    {
        if (!isset($this->username, $this->serverId)) {
            throw new \Lib\Exception('Missing required parameters', 400);
        }

        $users = \Lib\Models\User::find([
            'name' => $this->username,
            'auth_server_id' => $this->serverId,
        ], 1);
        if (empty($users)) {
            throw new \Lib\Exception('User not found or wrong serverId', 403);
        }

        $action = new GetProfile(reset($users));
        return $action->call();
    }
}
