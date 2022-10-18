<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use LaravelQueryFilter\Filter;

class LimitRecordsFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        if ($limit = intval($filter->data['limit'] ?? null)) {
            $filter->builder->limit($limit);
        }

        return $next($filter);
    }
}
