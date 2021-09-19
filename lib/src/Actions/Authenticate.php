<?php

namespace Lib\Actions;


use Lib\Exception;
use Lib\Models\User;
use Lib\UUID;
use Lib\Models\Session;

class Authenticate extends AbstractAction
{
    public function run(): ?array
    {
        $input = $this->getInput();
        if ($this->hasParam('refresh')) {
            if (!isset($input['token'])) {
                throw new Exception('Missing access token', 400);
            }
            $sessionUuid = new UUID($input['token']);
            $session = Session::load($sessionUuid);
            $user = $session->getUser();
        } else {
            if (!isset($input['username'], $input['password'])) {
                throw new Exception('Missing required parameters', 400);
            }
            $session = null;
            $user = $this->getUserByNameAndPass($input['username'], $input['password']);
        }

        if ($session) {
            $session->touch();
        } else {
            $session = Session::create([
                'user_id' => $user->getId(),
                'updated' => new \Lib\DateTime(),
            ]);
            $session->save();
        }

        return [
            'uuid' => $user->getId()->format(),
            'name' => $user->name,
            'avatarImage' => $this->getAvatarPayload($user),
            'accessToken' => (string) $session->getId(),
        ];
    }

    protected function getUserByNameAndPass(string $name, string $password) : User
    {
        $user = User::find(['name' => $name]);
        if (empty($user)) {
            throw new Exception('Username does not exists', 404);
        }
        $user = reset($user);
        if (!$user->getPassword()->verify($password)) {
            throw new Exception('Invalid password', 403);
        }
        return $user;
    }

    protected function getAvatarPayload(User $user) : string
    {
        $avatarId = $user->getAvatarId();
        if ($avatarId === null) {
            return '';
        }
        $filename = ASSETS_DIR . '/avatars/' . $user->getAvatarId()->format() . '.jpg';
        if (!is_file($filename)) {
            return '';
        }
        return base64_encode(file_get_contents($filename));
    }
}