<?php

namespace Lib\Actions;

class GetPrivileges extends AbstractAction
{
    protected function run(): ?array
    {
        //For now just implement static response
        return [
            "privileges" => [
                "onlineChat" => [
                    "enabled" => true,
                ],
                "multiplayerServer" => [
                    "enabled" => true,
                ],
                "multiplayerRealms" => [
                    "enabled" => false,
                ],
            ],
        ];
    }
}