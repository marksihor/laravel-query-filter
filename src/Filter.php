<?php

namespace LaravelQueryFilter;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Filter
{
    public BuilderContract $builder;
    public Model $model;
    public array $data;
    public string $table;

    public function __construct(BuilderContract $builder, array $data)
    {
        $this->builder = $builder;
        $this->model = $builder->getModel();
        $this->data = $data;
        $this->table = $this->model->getTable();
    }

    public function isRelationExists(string $relation): bool
    {
        return method_exists($this->model, $relation);
    }

    public function isRelationAllowed(string $relation): bool
    {
        if ($allowedRelations = $this->getModelSettings('relations')) {
            return in_array($relation, $allowedRelations);
        }

        return $allowedRelations === null;
    }

    public function isColumnExist(string $column): bool
    {
        if (Str::contains($column, '->')) {
            $column = explode('->', $column)[0];
            return Schema::hasColumn($this->table, $column) and $this->isColumnJson($column);
        }

        return Schema::hasColumn($this->table, $column);
    }

    public function isColumnJson(string $column): bool
    {
        return Schema::getColumnType($this->table, $column) === 'json';
    }

    public function getModelSettings(null|string $setting = null): null|array
    {
        $data = [];
        if ($configConstraint = config("laravel_query_filter.model_settings." . $this->model::class)) {
            if (is_callable($configConstraint)) {
                $data = $configConstraint();
            } else {
                $data = $configConstraint;
            }
        }

        return $setting ? ($data[$setting] ?? null) : $data;
    }
}
