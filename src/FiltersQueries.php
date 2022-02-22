<?php

namespace LaravelQueryFilter;

use Illuminate\Database\Eloquent\Builder;

trait FiltersQueries
{
    public function filter(Builder $builder, null|array $data = null): Builder
    {
        return (new FilterPipeline($builder, $data ?: request()->all()))->filter()->builder;
    }
}
