<?php

if (!function_exists('format_currency')) {
    /**
     * Format a number as currency
     */
    function format_currency($amount, $currency = 'LKR')
    {
        return $currency . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date string
     */
    function format_date($date, $format = 'd/m/Y')
    {
        if ($date instanceof \Carbon\Carbon) {
            return $date->format($format);
        }

        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('cents_to_currency')) {
    /**
     * Format currency (no conversion needed - kept for backward compatibility)
     */
    function cents_to_currency($amount)
    {
        return number_format($amount, 2);
    }
}

if (!function_exists('currency_to_cents')) {
    /**
     * Return currency as-is (no conversion needed - kept for backward compatibility)
     */
    function currency_to_cents($amount)
    {
        return $amount;
    }
}
