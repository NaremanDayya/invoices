<?php

namespace App\Traits;

trait HasColumnSelection
{
    /**
     * Get the default columns for this model's table view
     * Override this method in your controller or model
     * 
     * @return array
     */
    protected function getTableColumns()
    {
        return [];
    }

    /**
     * Get the storage key for column preferences
     * Override this method to customize the storage key
     * 
     * @return string
     */
    protected function getColumnStorageKey()
    {
        $modelName = class_basename($this);
        return strtolower($modelName) . '_columns';
    }

    /**
     * Filter data based on visible columns
     * 
     * @param array $data
     * @param array $visibleColumns
     * @return array
     */
    protected function filterByVisibleColumns(array $data, array $visibleColumns = null)
    {
        if (empty($visibleColumns)) {
            return $data;
        }

        $filtered = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $visibleColumns)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Check if a column is visible
     * 
     * @param string $columnKey
     * @param array $visibleColumns
     * @return bool
     */
    protected function isColumnVisible($columnKey, array $visibleColumns = null)
    {
        if (empty($visibleColumns)) {
            return true;
        }

        return in_array($columnKey, $visibleColumns);
    }
}
