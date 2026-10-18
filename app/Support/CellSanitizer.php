<?php

namespace App\Support;

/**
 * Neutralises spreadsheet formula injection ("=HYPERLINK(...)", "+cmd|...", "@SUM(...)").
 * Run EVERY imported text cell through clean() before saving it, and every exported text cell too.
 */
class CellSanitizer
{
    public static function clean($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim(strip_tags($value));

        // A leading = + - @ tab or CR would be treated as a formula by Excel/Sheets.
        if ($value !== '' && preg_match('/^[=+\-@\t\r]/', $value)) {
            $value = "'" . $value;
        }

        return $value;
    }
}