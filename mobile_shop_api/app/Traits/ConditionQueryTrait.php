<?php

namespace App\Traits;

use DateTime;
use Exception;

trait ConditionQueryTrait
{
    /**
     * Scope a query to only include data between 2 given date
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  date  $startDate
     * @param  date  $endDate
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfDate($query, $startDate, $endDate)
    {
        $formatDate = config('global.datetime.format');

        $fromDate = DateTime::createFromFormat($formatDate['input_date'], $startDate)->format($formatDate['start_date']);
        $toDate = DateTime::createFromFormat($formatDate['input_date'], $endDate)->format($formatDate['end_date']);

        return $query->whereBetween('created_at', [$fromDate, $toDate]);
    }

    /**
     * Scope a query to only include data of given search
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfSearch($query, $search)
    {
        return $query->where('name', 'LIKE', '%' . $search . '%');
    }

    /**
     * Scope a query to only include data of given paginate
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $paginationKey
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfPaginate($query, $paginationKey)
    {
        $paginatationPage = config('global.pagination.per_page');
        $maxRecord = config('global.pagination.max_record');
        $perPage = array_key_exists($paginationKey, $paginatationPage) == true ? $paginatationPage[$paginationKey] : $maxRecord;

        return $query->paginate($perPage);
    }

    /**
     * Scope a query to only include data of given column order
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $column
     * @param  int  $order
     * @param  array  $columns
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfOrderBy($query, $columnKey, $orderKey, $columns)
    {
        $sortOrder = ($orderKey == 1) ? 'DESC' : 'ASC';

        if (array_key_exists($columnKey, $columns)) {
            $sortColumn = $columns[$columnKey];
        } else {
            throw new Exception("Không tìm thấy trường này");
        }

        return $query->orderBy($sortColumn, $sortOrder);
    }

    /**
     * Function get data with order condition
     * 
     * @param  array $condition
     * @param  array $columns
     * 
     * @return array 
     */
    public function getCollectionDataWithOrder($condition, $columns)
    {
        $search = isset($condition['search']) ? $condition['search'] : '';
        $startDate = isset($condition['start_date']) ? $condition['start_date'] : config('global.datetime.default_date');
        $endDate = isset($condition['end_date']) ? $condition['end_date'] : now()->format(config('global.datetime.format.input_date'));

        return $this->ofSearch($search)
            ->ofDate($startDate, $endDate)
            ->ofOrderBy($condition['sort'], $condition['order'], $columns)
            ->ofPaginate($condition['per_page']);
    }
}
