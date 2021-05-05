<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductMultiSheetExport implements WithMultipleSheets
{
   /**
     * Instantiate a new controller instance
     *
     * @param  array $condition
     * 
     * @return void
     */
    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Create multiple sheets (1 sheet have 100 records)
     *
     * @return array
     */
    public function sheets(): array
    {
        $paginationKey = $this->condition['per_page'];
        $paginatationPage = config('global.pagination.per_page');
        $maxRecord = config('global.pagination.max_record');
        $perPage = array_key_exists($paginationKey, $paginatationPage) == true ? $paginatationPage[$paginationKey] : $maxRecord;

        $sheets = [];
        for ($page = 1; $page <= ceil($perPage / $maxRecord); $page++) {
            $sheets[] = new ProductExport($this->condition, $page);
        }
        return $sheets;
    }
}
