<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use LaravelQueryFilter\Filter;

class InitFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        // to select only allowed columns if SelectColumnsFilter is not triggered
        if (!($filter->data['select'] ?? null) && ($allowedColumns = $filter->getModelSettings('columns'))) {
            if (count($allowedColumns)) $filter->builder->select(array_map(function ($item) use ($filter) {
                return $filter->getPrefix() . $item;
            }, $allowedColumns));
        }

        return $next($filter);
    }
}
