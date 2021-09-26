<?php

namespace Lib\Actions;

use Lib\Exception;
use Lib\MojangAuth;

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
            throw new Exception('Missing required parameters', 400);
        }

        $users = \Lib\Models\User::find([
            'name' => $this->username,
        ], 1);
        if (empty($users)) {
            throw new Exception('User not found', 404);
        }

        $user = reset($users);
        $this->checkAccess($user);
        if ($user->auth_server_id !== $this->serverId) {
            if (!$user->isLinkedToMojang()) {
                throw new Exception('Authentication is incorrect or expired', 403);
            }
            return MojangAuth::instance()->serverJoined($this->username, $this->serverId);
        }

        $action = new GetProfile($user);
        return $action->call();
    }
}
