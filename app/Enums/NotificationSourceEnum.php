<?php

namespace App\Enums;

class NotificationSourceEnum
{
    const SYSTEM = 'SYSTEM';
    const DEVICE = 'DEVICE';

    const SYSTEM_ID = 1;
    const DEVICE_ID = 2;

    public static function cases()
    {
        return [
            ['name' => 'SYSTEM', 'value' => self::SYSTEM_ID],
            ['name' => 'DEVICE', 'value' => self::DEVICE_ID]
        ];
    }

    public static function getStatuses()
    {
        return [
            self::SYSTEM,
            self::DEVICE
        ];
    }

    public static function getStatusId($status)
    {
        switch ($status) {
            case self::SYSTEM:
                return self::SYSTEM_ID;
            case self::DEVICE:
                return self::DEVICE_ID;
            default:
                throw new \InvalidArgumentException("Invalid status: $status");
        }
    }

    public static function getIdStatus($id)
    {
        switch ($id) {
            case self::SYSTEM_ID:
                return self::SYSTEM;
            case self::DEVICE_ID:
                return self::DEVICE;
            default:
                throw new \InvalidArgumentException("Invalid ID: $id");
        }
    }
}
