<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use LaravelQueryFilter\Filter;
use LaravelQueryFilter\FilterPipeline;

class HasRelationsFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        $has = $filter->data['has'] ?? null;

        if (is_string($has)) {
            foreach (explode(',', $has) as $item) {
                $relations = explode('.', $item);
                $relation = array_shift($relations);
                $subRelations = $relations;

                if ($filter->isRelationExists($relation) && $filter->isRelationAllowed($relation)) {
                    $filter->builder->whereHas($relation, function ($query) use ($subRelations) {
                        if ($subRelations) {
                            (new FilterPipeline($query, ['has' => implode('.', $subRelations)]));
                        }
                    });
                } else {
                    $filter->builder->whereNull('id');
                }
            }
        } elseif (is_array($has)) {
            foreach ($has as $relation => $filters) {
                if ($filter->isRelationExists($relation) && $filter->isRelationAllowed($relation)) {
                    $filter->builder->whereHas($relation, function ($query) use ($filters) {
                        (new FilterPipeline($query, $filters));
                    });
                }
            }
        }

        return $next($filter);
    }
}
