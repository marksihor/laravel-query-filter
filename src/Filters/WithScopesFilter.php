<?php

namespace LaravelQueryFilter\Filters;

use Closure;
use Illuminate\Support\Str;
use LaravelQueryFilter\Filter;

class WithScopesFilter implements FilterInterface
{
    public function handle(Filter $filter, Closure $next)
    {
        $scopes = $filter->data['scopes'] ?? null;

        if (is_string($scopes)) {
            foreach (explode(',', $scopes) as $scope) {
                $methodName = 'scopePublic' . ucfirst(Str::camel($scope));
                $scopeName = Str::camel('public' . ucfirst($scope));

                if ($filter->isRelationExists($methodName)) {
                    $filter->builder->{$scopeName}();
                }
            }
        }

        return $next($filter);
    }
}
