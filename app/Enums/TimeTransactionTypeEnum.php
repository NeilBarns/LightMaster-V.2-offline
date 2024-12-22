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
}
