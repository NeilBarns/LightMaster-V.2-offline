<?php

namespace App\Enums;

class NotificationLevelEnum
{
    const NORMAL = 'NORMAL';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';

    const NORMAL_ID = 1;
    const WARNING_ID = 2;
    const ERROR_ID = 3;

    public static function cases()
    {
        return [
            ['name' => 'NORMAL', 'value' => self::NORMAL_ID],
            ['name' => 'WARNING', 'value' => self::WARNING_ID],
            ['name' => 'ERROR', 'value' => self::ERROR_ID]
        ];
    }

    public static function getStatuses()
    {
        return [
            self::NORMAL,
            self::WARNING,
            self::ERROR
        ];
    }

    public static function getStatusId($status)
    {
        switch ($status) {
            case self::NORMAL:
                return self::NORMAL_ID;
            case self::WARNING:
                return self::WARNING_ID;
            case self::ERROR:
                return self::ERROR_ID;
            default:
                throw new \InvalidArgumentException("Invalid status: $status");
        }
    }

    public static function getIdStatus($id)
    {
        switch ($id) {
            case self::NORMAL_ID:
                return self::NORMAL;
            case self::WARNING_ID:
                return self::WARNING;
            case self::ERROR_ID:
                return self::ERROR;
            default:
                throw new \InvalidArgumentException("Invalid ID: $id");
        }
    }
}
