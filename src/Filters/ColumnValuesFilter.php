<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use Illuminate\Support\Str;
use LaravelQueryFilter\Filter;
use LaravelQueryFilter\FilterPipeline;

class ColumnValuesFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        $skipKeys = ['with', 'select', 'order', 'orderBy', 'withCount', 'has', 'hasNot'];

        foreach ($filter->data as $k => $v) {
            $k = Str::replace('__', '->', $k);

            if (!in_array($k, $skipKeys)) {
                if ($filter->isColumnExist($k)) {
                    if ($v === null) {
                        $filter->builder->whereNull($k);
                    } elseif (is_string($v)) {
                        if (in_array($v, ['null', 'notNull', 'today', 'tomorrow', 'past', 'future', 'yesterday', 'day_before_yesterday'])) {
                            if ($v === 'null') {
                                $filter->builder->whereNull($k);
                            } elseif ($v === 'notNull') {
                                $filter->builder->whereNotNull($k);
                            } elseif ($v === 'today') {
                                $filter->builder->whereDate($k, now()->format('Y-m-d'));
                            } elseif ($v === 'yesterday') {
                                $filter->builder->whereDate($k, now()->subDay());
                            } elseif ($v === 'tomorrow') {
                                $filter->builder->whereDate($k, now()->addDay());
                            } elseif ($v === 'day_before_yesterday') {
                                $filter->builder->whereDate($k, now()->subDays(2));
                            } elseif ($v === 'future') {
                                $filter->builder->where($k, '>=', now());
                            } elseif ($v === 'past') {
                                $filter->builder->where($k, '<=', now());
                            }
                        } elseif (Str::startsWith($v, '%') or Str::endsWith($v, '%')) {
                            $filter->builder->where($k, 'like', $v);
                        } else {
                            $filter->builder->where($k, '=', $v);
                        }
                    } elseif (is_array($v)) {
                        if (key_exists('from', $v) and key_exists('to', $v)) {
                            $filter->builder->whereBetween($k, [$v['from'], $v['to']]);
                        } elseif (key_exists('from', $v)) {
                            $filter->builder->where($k, '>=', $v['from']);
                        } elseif (key_exists('to', $v)) {
                            $filter->builder->where($k, '<=', $v['to']);
                        }

                        if (key_exists('not_in', $v)) {
                            $filter->builder->whereNotIn($k, explode(',', $v['not_in']));
                        }

                        if (key_exists('in', $v)) {
                            $filter->builder->whereIn($k, explode(',', $v['in']));
                        }

                        if (key_exists('orderBy', $v) && in_array($v['orderBy'], ['asc', 'desc'])) {
                            (new FilterPipeline($filter->builder, ['orderBy' => $k, 'order' => $v['orderBy']]));
                        }
                    }
                } elseif ($filter->isRelationExists($k) && $filter->isRelationAllowed($k)) {
                    if (is_array($v)) {
                        $filter->builder->whereHas($k, function ($query) use ($v) {
                            (new FilterPipeline($query, $v));
                        });
                    }
                }
            }
        }

        return $next($filter);
    }
}
