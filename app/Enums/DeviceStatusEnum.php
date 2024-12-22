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

    const PENDING_ID = 1;
    const RUNNING_ID = 2;
    const INACTIVE_ID = 3;
    const DISABLED_ID = 4;
    const PAUSE_ID = 5;
    const RESUME_ID = 6;
    const STARTFREE_ID = 7;
    const ENDFREE_ID = 8;

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
            self::ENDFREE
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
            case self::PAUSE:
                return self::PAUSE_ID;
            case self::RESUME:
                return self::RESUME_ID;
            case self::STARTFREE:
                return self::STARTFREE_ID;
            case self::ENDFREE:
                return self::ENDFREE_ID;
            default:
                throw new \InvalidArgumentException("Invalid ID: $id");
        }
    }
}
