<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class Filters
{

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * @param  string|null  $direction
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orderBy(Builder $query, string $column, ?string $direction = 'asc')
    {
        if ($direction === 'desc') {
            $query->orderByDesc($column);
        } else {
            $query->orderBy($column);
        }

        return $query;
    }
}