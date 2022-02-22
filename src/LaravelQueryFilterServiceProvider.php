<?php

namespace LaravelQueryFilter;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;


class LaravelQueryFilterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/' => config_path()
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/config/laravel_query_filter.php', 'laravel_query_filter');
    }
}
