<?php

namespace App\Enums;

class TimeTransactionTypeEnum
{
    const START = 'Start';
    const END = 'End';
    const EXTEND = 'Extend';
    const PAUSE = 'Pause';
    const RESUME = "Resume";
    const STARTFREE = "Start Free";
    const ENDFREE = "End Free";

    const DEVICE_ID = 1;
    const USER_ID = 2;
    const ROLE_ID = 3;

    public static function cases()
    {
        return [
            ['name' => 'START', 'value' => self::START],
            ['name' => 'END', 'value' => self::END],
            ['name' => 'EXTEND', 'value' => self::EXTEND],
            ['name' => 'PAUSE', 'value' => self::PAUSE],
            ['name' => 'RESUME', 'value' => self::RESUME],
            ['name' => 'STARTFREE', 'value' => self::STARTFREE],
            ['name' => 'ENDFREE', 'value' => self::ENDFREE],
        ];
    }
}
