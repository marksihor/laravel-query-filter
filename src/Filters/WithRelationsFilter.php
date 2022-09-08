<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use Illuminate\Support\Str;
use LaravelQueryFilter\Filter;
use LaravelQueryFilter\FilterPipeline;

class WithRelationsFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        $with = $filter->data['with'] ?? null;

        if (is_string($with)) {
            foreach (explode(',', $with) as $item) {
                $relations = explode('.', $item);
                $relation = Str::camel(array_shift($relations));
                $subRelations = $relations;

                if ($filter->isRelationExists($relation) && $filter->isRelationAllowed($relation)) {
                    $filter->builder->with($relation, function ($query) use ($subRelations) {
                        $filters = [];
                        if ($subRelations) {
                            $filters['with'] = implode('.', $subRelations);
                        }
                        (new FilterPipeline($query, $filters));
                    });
                }
            }
        } elseif (is_array($with)) {
            foreach ($with as $relation => $filters) {
                $relation = Str::camel($relation);
                if ($filter->isRelationExists($relation) && $filter->isRelationAllowed($relation)) {
                    $filter->builder->with($relation, function ($query) use ($filter, $filters) {
                        (new FilterPipeline($query, is_array($filters) ? $filters : []));
                    });
                }
            }
        }

        return $next($filter);
    }
}
