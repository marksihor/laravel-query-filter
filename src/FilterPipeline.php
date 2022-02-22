<?php

namespace LaravelQueryFilter;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Pipeline\Pipeline;

class FilterPipeline
{
    private Filter $pipeline;

    public function __construct(BuilderContract $builder, array $data)
    {
        $this->pipeline = app(Pipeline::class)
            ->send(new Filter($builder, $data))
            ->through(config('laravel_query_filter.filters'))
            ->thenReturn();
    }

    public function filter(): Filter
    {
        return $this->pipeline;
    }
}
