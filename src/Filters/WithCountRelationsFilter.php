<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use LaravelQueryFilter\Filter;
use LaravelQueryFilter\FilterPipeline;

class WithCountRelationsFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        $withCount = $filter->data['withCount'] ?? null;
        if (is_string($withCount)) {
            $relations = explode(',', $withCount);
            foreach ($relations as $relation) {
                if ($filter->isRelationExists($relation) && $filter->isRelationAllowed($relation)) {
                    $filter->builder->withCount($relation);
                }
            }
        } elseif (is_array($withCount)) {
            foreach ($withCount as $relation => $filters) {
                if ($filter->isRelationExists($relation) && $filter->isRelationAllowed($relation)) {
                    $filter->builder->withCount([$relation => function ($query) use ($filters) {
                        (new FilterPipeline($query, $filters));
                    }]);
                }
            }
        }

        return $next($filter);
    }
}
