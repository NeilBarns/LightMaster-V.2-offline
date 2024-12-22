<?php

if (!function_exists('convertMinutesToHoursAndMinutes')) {
    function convertMinutesToHoursAndMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return "{$hours} hr(s) " . "{$remainingMinutes} min(s)";
        } elseif ($hours > 0) {
            return "{$hours} hr(s)";
        } else {
            return "{$remainingMinutes} min(s)";
        }
    }
}

if (!function_exists('convertTo12HourFormat')) {
    function convertTo12HourFormat($timeString)
    {
        if (is_string($timeString)) {
            try {
                $time = \Carbon\Carbon::createFromFormat('H:i:s', $timeString);
                return $time->format('g:i:s A');
            } catch (\Exception $e) {
                return 'N/A';
            }
        }
        return 'N/A';
    }
}

if (!function_exists('convertSecondsToTimeFormat')) {
    function convertSecondsToTimeFormat($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        $timeString = '';

        if ($hours > 0) {
            $timeString .= $hours . ' hr' . ($hours > 1 ? 's' : '') . ' ';
        }
        if ($minutes > 0) {
            $timeString .= $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ';
        }
        if ($remainingSeconds > 0) {
            $timeString .= $remainingSeconds . ' sec' . ($remainingSeconds > 1 ? 's' : '');
        }

        return trim($timeString);
    }
}
