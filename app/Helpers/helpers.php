<?php

if (!function_exists('rwf')) {
    /**
     * Format a number as Rwandan Franc currency
     *
     * @param float|int $amount
     * @param int $decimals
     * @return string
     */
    function rwf($amount, $decimals = 0): string
    {
        return 'RWF ' . number_format($amount, $decimals);
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format a number as currency with configurable symbol
     *
     * @param float|int $amount
     * @param string $symbol
     * @param int $decimals
     * @return string
     */
    function format_currency($amount, $symbol = 'RWF', $decimals = 0): string
    {
        return $symbol . ' ' . number_format($amount, $decimals);
    }
}
