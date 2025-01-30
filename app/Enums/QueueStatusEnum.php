<?php

namespace App\Enums;

class QueueStatusEnum
{
    const ACTIVE = 'Active';
    const COMPLETED = 'Completed';
    const ERROR = 'Error';
    const PENDING = 'Pending';

    const ACTIVE_ID = 1;
    const COMPLETED_ID = 2;
    const ERROR_ID = 3;
    const PENDING_ID = 4;

    public static function cases()
    {
        return [
            ['name' => 'ACTIVE', 'value' => self::ACTIVE_ID],
            ['name' => 'COMPLETED', 'value' => self::COMPLETED_ID],
            ['name' => 'ERROR', 'value' => self::ERROR_ID],
            ['name' => 'PENDING', 'value' => self::PENDING_ID],
        ];
    }

    public static function getStatuses()
    {
        return [
            self::ACTIVE,
            self::COMPLETED,
            self::ERROR,
            self::PENDING
        ];
    }

    public static function getStatusId($status)
    {
        switch ($status) {
            case self::ACTIVE:
                return self::ACTIVE_ID;
            case self::COMPLETED:
                return self::COMPLETED_ID;
            case self::ERROR:
                return self::ERROR_ID;
            case self::PENDING:
                return self::PENDING_ID;
            default:
                throw new \InvalidArgumentException("Invalid status: $status");
        }
    }

    public static function getIdStatus($id)
    {
        switch ($id) {
            case self::ACTIVE_ID:
                return self::ACTIVE;
            case self::COMPLETED_ID:
                return self::COMPLETED;
            case self::ERROR_ID:
                return self::ERROR;
            case self::PENDING_ID:
                return self::PENDING;
            default:
                throw new \InvalidArgumentException("Invalid ID: $id");
        }
    }
}