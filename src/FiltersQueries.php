<?php

namespace LaravelQueryFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

trait FiltersQueries
{
    public function filter(Builder $builder, null|array $data = null): Builder
    {
        return (new FilterPipeline($builder, $data ?: request()->all()))->filter()->builder;
    }

    public function filterAndCache(
        Builder    $builder,
        null|int   $perPage,
        array      $cacheTags = [],
        null|int   $cacheSeconds = 86400,
        null|array $data = null
    ): LengthAwarePaginator
    {
        $data = $data ?: request()->all();
        if (count($cacheTags)) {
            $cacheKey = md5($builder->toSql() . json_encode($data) . json_encode(['per_page' => $perPage]));
            return Cache::tags($cacheTags)->remember($cacheKey, $cacheSeconds, function () use ($builder, $data, $perPage) {
                return (new FilterPipeline($builder, $data))->filter()->builder->paginate($perPage);
            });
        }
    }
}
