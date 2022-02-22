<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use LaravelQueryFilter\Filter;

class SelectColumnsFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        $select = array_filter(explode(',', $filter->data['select'] ?? ''));
        if ($allowedColumns = $filter->getModelSettings('columns')) {
            if (count($select)) {
                $select = array_intersect($select, $allowedColumns);
            } else {
                $select = $allowedColumns;
            }
        }

        if (count($select)) $filter->builder->select($select);

        return $next($filter);
    }
}
