<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use LaravelQueryFilter\Filter;
use LaravelQueryFilter\FilterPipeline;

class SelectColumnsFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        if ($select = $filter->data['select'] ?? null) {
            if (is_string($select)) {
                $select = array_filter(explode(',', $select), function ($column) use ($filter) {
                    return $filter->isColumnExist($column);
                });
                if ($allowedColumns = $filter->getModelSettings('columns')) {
                    if (count($select)) {
                        $select = array_intersect($select, $allowedColumns);
                    } else {
                        $select = $allowedColumns;
                    }
                }

                if (count($select)) $filter->builder->select(array_map(function ($item) use ($filter) {
                    return $filter->getPrefix() . $item;
                }, $select));
            } elseif (is_array($select)) {
                foreach ($select as $relation => $columns)
                    (new FilterPipeline($filter->builder, [
                        'with' => [
                            $relation => [
                                'select' => $columns
                            ]
                        ]
                    ]));
            }
        }

        return $next($filter);
    }
}
