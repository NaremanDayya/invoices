<?php

namespace App\Traits;

trait FormatsIntegers
{
    /**
     * Format a value as an integer if it represents a whole number
     * 
     * @param mixed $value
     * @return int|float
     */
    public function formatAsInteger($value)
    {
        if (is_null($value)) {
            return 0;
        }

        $numericValue = is_numeric($value) ? floatval($value) : 0;
        
        // Check if the value is a whole number (no decimal part)
        if (floor($numericValue) == $numericValue) {
            return (int) $numericValue;
        }
        
        return $numericValue;
    }

    /**
     * Format multiple attributes as integers
     * 
     * @param array $attributes
     * @return array
     */
    public function formatIntegerAttributes(array $attributes)
    {
        $formatted = [];
        
        foreach ($attributes as $key => $value) {
            $formatted[$key] = $this->formatAsInteger($value);
        }
        
        return $formatted;
    }

    /**
     * Get integer fields that should always be formatted as integers
     * Override this method in your model to specify which fields
     * 
     * @return array
     */
    protected function getIntegerFields()
    {
        return [
            'total_workers',
            'total_supervisors',
            'total_managers',
            'total_users',
            'work_days',
            'employees_count',
            'work_days_count',
            'workers_days',
            'supervisors_days',
            'managers_days',
            'users_days',
            'issue_delay_days',
            'payment_delay_days',
            'credit_notes_count',
            'allowed_late_pay_days',
            'employee_count_difference',
            'work_days_difference',
        ];
    }

    /**
     * Apply integer formatting to specified fields in the model
     */
    public function applyIntegerFormatting()
    {
        $integerFields = $this->getIntegerFields();
        
        foreach ($integerFields as $field) {
            if (isset($this->attributes[$field])) {
                $this->attributes[$field] = $this->formatAsInteger($this->attributes[$field]);
            }
        }
    }

    /**
     * Override getAttribute to format integers on retrieval
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        
        if (in_array($key, $this->getIntegerFields())) {
            return $this->formatAsInteger($value);
        }
        
        return $value;
    }
}
