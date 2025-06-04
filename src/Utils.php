<?php

namespace Stronghold\Bench;

use DateInterval;

/**
 * Utility class for benchmarks
 */
class Utils
{
    /**
     * Calculate seconds from DateTime diff
     *
     * @param DateInterval $diff DateTime difference
     * @return float Total seconds
     */
    public static function calculateSeconds(DateInterval $diff): float
    {
        $seconds = $diff->h * 3600 + $diff->i * 60 + $diff->s;

        // Prevent division by zero - if time is 0, use a small value instead
        return ($seconds > 0) ? $seconds : 0.001;
    }

    /**
     * Calculate and format MB per second
     *
     * @param int $sizeMB Size in MB
     * @param float $seconds Time in seconds
     * @return string Formatted MB/s
     */
    public static function formatSpeed(int $sizeMB, float $seconds): string
    {
        $mbps = $sizeMB / $seconds;

        return sprintf('%.2f MB/s', $mbps);
    }

    /**
     * Format time difference
     *
     * @param DateInterval $diff DateTime difference
     * @return string Formatted time
     */
    public static function formatTime(DateInterval $diff): string
    {
        return sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);
    }
}
