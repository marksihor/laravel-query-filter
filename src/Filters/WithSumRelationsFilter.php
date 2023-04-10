<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use Illuminate\Support\Str;
use LaravelQueryFilter\Filter;

class WithSumRelationsFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        $withCount = $filter->data['withSum'] ?? null;
        if (is_string($withCount)) {
            $items = explode(',', $withCount);
            foreach ($items as $item) {
                if (Str::substrCount($item, '.') == 1) {
                    $relation = Str::camel(explode('.', $item)[0]);
                    $column = explode('.', $item)[1];
                    if ($column && $filter->isRelationExists($relation) && $filter->isRelationAllowed($relation)) {
                        $filter->builder->withSum($relation, $column);
                    }
                }
            }
        }

        return $next($filter);
    }
}
