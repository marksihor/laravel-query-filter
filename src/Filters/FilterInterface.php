<?php

namespace LaravelQueryFilter\Filters;

use LaravelQueryFilter\Filter;
use Closure;

interface FilterInterface
{
    public function handle(Filter $filter, Closure $next);
}
