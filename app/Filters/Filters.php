<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class Filters
{
    private Builder $query;

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return $this
     */
    public function setQuery(Builder $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param  string  $column
     * @param  string|null  $direction
     *
     * @return $this
     */
    public function orderBy(string $column, ?string $direction)
    {
        if ($direction === 'desc') {
            $this->query->orderByDesc($column);
        } else {
            $this->query->orderBy($column);
        }

        return $this;
    }
}