<?php

namespace LaravelQueryFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
        array      $cacheTags,
        null|int   $cacheSeconds = 86400,
        null|array $data = null,
        bool       $paginate = true,
        null|array $additionalData = [] // for more options to make unique cacheKey
    ): LengthAwarePaginator|Collection
    {
        $data = $data ?: request()->all();
        if (count($cacheTags)) {
            $cacheKey = md5(
                $builder->toSql() .
                json_encode($data) .
                json_encode($additionalData) .
                json_encode(['per_page' => $perPage]) .
                intval($paginate) .
                request()->url()
            );
            return Cache::tags($cacheTags)->remember($cacheKey, $cacheSeconds, function () use ($builder, $data, $perPage, $paginate, $cacheKey) {
                if ($paginate) {
                    return (new FilterPipeline($builder, $data))->filter()->builder->paginate($perPage);
                } else {
                    return (new FilterPipeline($builder, $data))->filter()->builder->get();
                }
            });
        } else {
            throw new \Exception('Cache tags is not provided');
        }
    }
}
