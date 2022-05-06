<?php

namespace LaravelQueryFilter\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait CachesFilterQueriesResultTrait
{
    protected static function bootCachesFilterQueriesResultTrait()
    {
        static::created(fn($model) => self::process($model));
        static::updated(fn($model) => self::process($model));
        static::deleted(fn($model) => self::process($model));
    }

    private static function process(Model $model): void
    {
        if (property_exists($model, 'queryFilterCacheTags') && is_array($model->queryFilterCacheTags)) {
            Cache::tags($model->queryFilterCacheTags)->flush();
        } else {
            throw new \Exception("The {$model->getMorphClass()} queryFilterCacheTags array is not set up.");
        }
    }
}
