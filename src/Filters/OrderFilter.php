<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use Illuminate\Support\Str;
use LaravelQueryFilter\Filter;

class OrderFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        if (
            key_exists('order', $filter->data) and
            key_exists('orderBy', $filter->data) and
            in_array($filter->data['order'], ['asc', 'desc'])
        ) {
            $k = Str::replace('__', '->', $filter->data['orderBy']);
            if ($filter->isColumnExist($k)) {
                $filter->builder->orderBy($k, $filter->data['order']);
            }
        }

        return $next($filter);
    }
}
