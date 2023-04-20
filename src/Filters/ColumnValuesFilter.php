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
                    $columnWithTablePrefix = $filter->getPrefix() . $k;
                    if ($v === null) {
                        $filter->builder->whereNull($columnWithTablePrefix);
                    } elseif (is_string($v)) {
                        if (in_array($v, ['null', 'notNull', 'today', 'tomorrow', 'past', 'future', 'yesterday', 'day_before_yesterday'])) {
                            if ($v === 'null') {
                                $filter->builder->whereNull($columnWithTablePrefix);
                            } elseif ($v === 'notNull') {
                                $filter->builder->whereNotNull($columnWithTablePrefix);
                            } elseif ($v === 'today') {
                                $filter->builder->whereDate($columnWithTablePrefix, now()->format('Y-m-d'));
                            } elseif ($v === 'yesterday') {
                                $filter->builder->whereDate($columnWithTablePrefix, now()->subDay());
                            } elseif ($v === 'tomorrow') {
                                $filter->builder->whereDate($columnWithTablePrefix, now()->addDay());
                            } elseif ($v === 'day_before_yesterday') {
                                $filter->builder->whereDate($columnWithTablePrefix, now()->subDays(2));
                            } elseif ($v === 'future') {
                                $filter->builder->where($columnWithTablePrefix, '>=', now());
                            } elseif ($v === 'past') {
                                $filter->builder->where($columnWithTablePrefix, '<=', now());
                            }
                        } elseif (Str::startsWith($v, '%') or Str::endsWith($v, '%')) {
                            $filter->builder->where($columnWithTablePrefix, 'like', $v);
                        } else {
                            $filter->builder->where($columnWithTablePrefix, '=', $v);
                        }
                    } elseif (is_array($v)) {
                        if (key_exists('from', $v) and key_exists('to', $v)) {
                            $filter->builder->whereBetween($columnWithTablePrefix, [$v['from'], $v['to']]);
                        } elseif (key_exists('from', $v)) {
                            $filter->builder->where($columnWithTablePrefix, '>=', $v['from']);
                        } elseif (key_exists('to', $v)) {
                            $filter->builder->where($columnWithTablePrefix, '<=', $v['to']);
                        } elseif (key_exists('between', $v) && Str::contains($v['between'], ',')) {
                            $between = explode(',', $v['between']);
                            if ($between[0] && $between[1]) {
                                $filter->builder->whereBetween($columnWithTablePrefix, [$between[0], $between[1]]);
                            } elseif ($between[0]) {
                                $filter->builder->where($columnWithTablePrefix, '>=', $between[0]);
                            } elseif ($between[1]) {
                                $filter->builder->where($columnWithTablePrefix, '<=', $between[1]);
                            }
                        }

                        if (key_exists('not_in', $v)) {
                            $filter->builder->whereNotIn($columnWithTablePrefix, explode(',', $v['not_in']));
                        }

                        if (key_exists('in', $v)) {
                            $filter->builder->whereIn($columnWithTablePrefix, explode(',', $v['in']));
                        }

                        if (key_exists('orderBy', $v) && in_array($v['orderBy'], ['asc', 'desc'])) {
                            (new FilterPipeline($filter->builder, ['orderBy' => $k, 'order' => $v['orderBy']]));
                        }
                    }
                } else {
                    $relationshipName = Str::camel($k);
                    if ($filter->isRelationExists($relationshipName) && $filter->isRelationAllowed($relationshipName)) {
                        if (is_array($v)) {
                            $filter->builder->whereHas($relationshipName, function ($query) use ($v) {
                                (new FilterPipeline($query, $v));
                            });
                        }
                    }
                }
            }
        }

        return $next($filter);
    }
}
