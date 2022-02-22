<?php

namespace LaravelQueryFilter\Filters;

use LaravelQueryFilter\Filter;
use Closure;

class OrderFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        if (
            key_exists('order', $filter->data) and
            key_exists('orderBy', $filter->data) and
            in_array($filter->data['order'], ['asc', 'desc'])
        ) {
            $filter->builder->orderBy($filter->data['orderBy'], $filter->data['order']);
        }

        return $next($filter);
    }
}
