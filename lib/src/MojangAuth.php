<?php

namespace Lib;

use GuzzleHttp\Client;

class MojangAuth
{
    use TSingleton;

    protected const SERVER_ENDPOINT = 'https://sessionserver.mojang.com/session/minecraft/hasJoined';

    protected Client $http;

    public function __construct()
    {
        $this->http = new Client();
    }

    public function serverJoined(string $username, string $serverId) : array
    {
        $response = $this->http->request('GET', self::SERVER_ENDPOINT, [
            'query' => [
                'username' => $username,
                'serverId' => $serverId,
            ],
        ]);
        $result = $response->getBody()->getContents();
        if ($response->getStatusCode() !== 200) {
            throw new Exception($result, $response->getStatusCode());
        }
        return json_decode($result, true);
    }
}