<?php

declare(strict_types=1);

namespace App\Service;

class FormatIntervalService
{
    public static function formatIntervalFromSecondsToString($timeIntervalInSeconds): string
    {
        $hours = floor($timeIntervalInSeconds / 3600);
        $minutes = floor(($timeIntervalInSeconds % 3600) / 60);
        $seconds = $timeIntervalInSeconds % 60;

        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public static function formatIntervalFromStringToSeconds($timeIntervalInString): int
    {
        [$hours, $minutes, $seconds] = explode(':', $timeIntervalInString);

        return (int) $hours * 3600 + (int) $minutes * 60 + (int) $seconds;
    }
}
