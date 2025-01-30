<?php

namespace App\Enums;

class ExchangeStatusEnum
{
    const PENDING = 'Pending';
    const COMPLETED = 'Completed';
    const ERROR = 'Error';
    const CANCELLED = 'Cancelled';

    const PENDING_ID = 1;
    const COMPLETED_ID = 2;
    const ERROR_ID = 3;
    const CANCELLED_ID = 4;

    public static function cases()
    {
        return [
            ['name' => 'PENDING', 'value' => self::PENDING_ID],
            ['name' => 'COMPLETED', 'value' => self::COMPLETED_ID],
            ['name' => 'ERROR', 'value' => self::ERROR_ID],
            ['name' => 'CANCELLED', 'value' => self::CANCELLED_ID],
        ];
    }

    public static function getStatuses()
    {
        return [
            self::PENDING,
            self::COMPLETED,
            self::ERROR,
            self::CANCELLED
        ];
    }

    public static function getStatusId($status)
    {
        switch ($status) {
            case self::PENDING:
                return self::PENDING_ID;
            case self::COMPLETED:
                return self::COMPLETED_ID;
            case self::ERROR:
                return self::ERROR_ID;
            case self::CANCELLED:
                return self::CANCELLED_ID;
            default:
                throw new \InvalidArgumentException("Invalid status: $status");
        }
    }

    public static function getIdStatus($id)
    {
        switch ($id) {
            case self::PENDING_ID:
                return self::PENDING;
            case self::COMPLETED_ID:
                return self::COMPLETED;
            case self::ERROR_ID:
                return self::ERROR;
            case self::CANCELLED_ID:
                return self::CANCELLED;
            default:
                throw new \InvalidArgumentException("Invalid ID: $id");
        }
    }
}