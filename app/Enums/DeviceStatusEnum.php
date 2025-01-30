<?php

namespace App\Enums;

class DeviceStatusEnum
{
    const PENDING = 'Pending Configuration';
    const RUNNING = 'Running';
    const INACTIVE = 'Inactive';
    const DISABLED = 'Disabled';
    const PAUSE = 'Pause';
    const RESUME = 'Resume';
    const STARTFREE = 'Start Free';
    const ENDFREE = 'End Free';
    const PENDINGDELETE = 'Pending Deletion';
    const DELETED = 'Deleted';
    const PENDINGEXCHANGE = 'Pending Exchange';

    const PENDING_ID = 1;
    const RUNNING_ID = 2;
    const INACTIVE_ID = 3;
    const DISABLED_ID = 4;
    const PAUSE_ID = 5;
    const RESUME_ID = 6;
    const STARTFREE_ID = 7;
    const ENDFREE_ID = 8;
    const PENDINGDELETE_ID = 9;
    const DELETED_ID = 10;
    const PENDINGEXCHANGE_ID = 11;

    public static function cases()
    {
        return [
            ['name' => 'PENDING', 'value' => self::PENDING_ID],
            ['name' => 'RUNNING', 'value' => self::RUNNING_ID],
            ['name' => 'INACTIVE', 'value' => self::INACTIVE_ID],
            ['name' => 'DISABLED', 'value' => self::DISABLED_ID],
            ['name' => 'PAUSE', 'value' => self::PAUSE_ID],
            ['name' => 'RESUME', 'value' => self::RESUME_ID],
            ['name' => 'STARTFREE', 'value' => self::STARTFREE_ID],
            ['name' => 'ENDFREE', 'value' => self::ENDFREE_ID],
            ['name' => 'PENDINGDELETE', 'value' => self::PENDINGDELETE_ID],
            ['name' => 'DELETED', 'value' => self::DELETED_ID],
            ['name' => 'PENDINGEXCHANGE', 'value' => self::PENDINGEXCHANGE_ID],
        ];
    }

    public static function getStatuses()
    {
        return [
            self::PENDING,
            self::RUNNING,
            self::INACTIVE,
            self::DISABLED,
            self::PAUSE,
            self::RESUME,
            self::STARTFREE,
            self::ENDFREE,
            self::PENDINGDELETE,
            self::DELETED,
            self::PENDINGEXCHANGE_ID
        ];
    }

    public static function getStatusId($status)
    {
        switch ($status) {
            case self::PENDING:
                return self::PENDING_ID;
            case self::RUNNING:
                return self::RUNNING_ID;
            case self::INACTIVE:
                return self::INACTIVE_ID;
            case self::DISABLED:
                return self::DISABLED_ID;
            case self::PAUSE:
                return self::PAUSE_ID;
            case self::RESUME:
                return self::RESUME_ID;
            case self::STARTFREE:
                return self::STARTFREE_ID;
            case self::ENDFREE:
                return self::ENDFREE_ID;
            case self::PENDINGDELETE:
                return self::PENDINGDELETE_ID;
            case self::DELETED:
                return self::DELETED_ID;
            case self::PENDINGEXCHANGE:
                return self::PENDINGEXCHANGE_ID;
            default:
                throw new \InvalidArgumentException("Invalid status: $status");
        }
    }

    public static function getIdStatus($id)
    {
        switch ($id) {
            case self::PENDING_ID:
                return self::PENDING;
            case self::RUNNING_ID:
                return self::RUNNING;
            case self::INACTIVE_ID:
                return self::INACTIVE;
            case self::DISABLED_ID:
                return self::DISABLED;
            case self::PAUSE_ID:
                return self::PAUSE;
            case self::RESUME_ID:
                return self::RESUME;
            case self::STARTFREE_ID:
                return self::STARTFREE;
            case self::ENDFREE_ID:
                return self::ENDFREE;
            case self::PENDINGDELETE_ID:
                return self::PENDINGDELETE;
            case self::DELETED_ID:
                return self::DELETED;
            case self::PENDINGEXCHANGE_ID:
                return self::PENDINGEXCHANGE;
            default:
                throw new \InvalidArgumentException("Invalid ID: $id");
        }
    }
}
