<?php

namespace App\Enums;

class DeviceHeartbeatStatusEnum
{
    const ONLINE = 'ONLINE';
    const OFFLINE = 'OFFLINE';

    public static function cases()
    {
        return [
            ['name' => 'ONLINE', 'value' => self::ONLINE],
            ['name' => 'OFFLINE', 'value' => self::OFFLINE]
        ];
    }
}