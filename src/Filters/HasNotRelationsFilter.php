<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use Illuminate\Support\Str;
use LaravelQueryFilter\Filter;
use LaravelQueryFilter\FilterPipeline;

class HasNotRelationsFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        $hasNot = $filter->data['hasNot'] ?? null;

        if (is_string($hasNot)) {
            foreach (explode(',', $hasNot) as $item) {
                $relations = explode('.', $item);
                $relation = Str::camel(array_shift($relations));
                $subRelations = $relations;

                if ($filter->isRelationExists($relation) && $filter->isRelationAllowed($relation)) {
                    $filter->builder->whereDoesntHave($relation, function ($query) use ($subRelations) {
                        if ($subRelations) {
                            (new FilterPipeline($query, ['hasNot' => implode('.', $subRelations)]));
                        }
                    });
                }
            }
        } elseif (is_array($hasNot)) {
            foreach ($hasNot as $relation => $filters) {
                $relation = Str::camel($relation);
                if ($filter->isRelationExists($relation) && $filter->isRelationAllowed($relation)) {
                    $filter->builder->whereDoesntHave($relation, function ($query) use ($filters) {
                        (new FilterPipeline($query, $filters));
                    });
                }
            }
        }

        return $next($filter);
    }
}
