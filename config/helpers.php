<?php

if (!function_exists('human_diff')) {
    function human_diff($startDate, $endDate)
    {
        if ($startDate == null || $endDate == null) {
            return null;
        }
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        return $end->longAbsoluteDiffForHumans($start);
    }
}

if (!function_exists('human_date_with_time')) {
    function human_date_with_time($date)
    {
        if ($date == null) return null;
        return Carbon\Carbon::parse($date)->format('d.m.Y H:i');
    }
}

if (!function_exists('human_date')) {
    function human_date($date)
    {
        if ($date == null) return null;
        return Carbon\Carbon::parse($date)->format('d.m.Y');
    }
}

if (!function_exists('format_gender')) {
    function format_gender($gender)
    {
        return match ($gender) {
            0 => 'Ж',
            1 => 'М',
            default => null,
        };
    }
}

if (!function_exists('format_phone')) {
    function format_phone($phoneNumber)
    {
        if ($phoneNumber === null) return null;

        $cleanedNumber = preg_replace('/\D/', '', $phoneNumber);
        return '+' . substr($cleanedNumber, 0, 3) . ' ' .
            substr($cleanedNumber, 3, 2) . ' ' .
            substr($cleanedNumber, 5, 3) . '-' .
            substr($cleanedNumber, 8, 2) . '-' .
            substr($cleanedNumber, 10);
    }
}

if (!function_exists('human_date_iso')) {
    function human_date_iso($date)
    {
        if ($date == null) return null;
        return Carbon\Carbon::parse($date)->locale('ru')->isoFormat('D MMMM Y');
    }
}

if (!function_exists('nameShortened')) {
    function nameShortened($fullName)
    {
        $name = mb_convert_encoding($fullName, 'UTF-8', 'UTF-8');
        $parts = explode(' ', $name);

        if (count($parts) >= 1) {
            $shortened = $parts[0];

            if (count($parts) >= 2) {
                $shortened .= ' ' . mb_substr($parts[1], 0, 1) . '.';
            }
            return $shortened;
        }
        return $fullName;
    }
}

if (!function_exists('human_duration')) {
    function human_duration($seconds)
    {
        if ($seconds == null) return null;
        return \Carbon\CarbonInterval::fromString($seconds . ' seconds')->cascade()->forHumans();
    }
}
