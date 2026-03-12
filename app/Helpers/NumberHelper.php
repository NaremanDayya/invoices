<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Format a number as an integer if it's a whole number, otherwise keep decimals
     * 
     * @param mixed $value
     * @param int $decimals Number of decimal places to show if not a whole number
     * @return string
     */
    public static function formatSmart($value, $decimals = 2)
    {
        if (is_null($value) || !is_numeric($value)) {
            return '0';
        }

        $numericValue = floatval($value);
        
        // Check if it's a whole number
        if (floor($numericValue) == $numericValue) {
            return number_format((int) $numericValue, 0);
        }
        
        return number_format($numericValue, $decimals);
    }

    /**
     * Format a value as a pure integer (always remove decimals)
     * 
     * @param mixed $value
     * @return int
     */
    public static function toInteger($value)
    {
        if (is_null($value) || !is_numeric($value)) {
            return 0;
        }

        $numericValue = floatval($value);
        
        // Check if it's a whole number
        if (floor($numericValue) == $numericValue) {
            return (int) $numericValue;
        }
        
        // Round to nearest integer for non-whole numbers
        return (int) round($numericValue);
    }

    /**
     * Format currency with smart integer handling
     * 
     * @param mixed $value
     * @param string $currency
     * @return string
     */
    public static function formatCurrency($value, $currency = 'ر.س')
    {
        return self::formatSmart($value, 0) . ' ' . $currency;
    }

    /**
     * Check if a value should be treated as an integer
     * 
     * @param mixed $value
     * @return bool
     */
    public static function isWholeNumber($value)
    {
        if (!is_numeric($value)) {
            return false;
        }

        $numericValue = floatval($value);
        return floor($numericValue) == $numericValue;
    }
}
