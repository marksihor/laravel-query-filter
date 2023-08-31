<?php

namespace LaravelQueryFilter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Filter
{
    /**
     * @var Builder $builder
     */
    public $builder;
    public Model $model;
    public array $data;
    public string $table;
    public ?array $filterableColumns;

    public function __construct($builder, array $data)
    {
        $this->builder = $builder;
        $this->model = $builder->getModel();
        $this->data = $data;
        $this->table = $this->model->getTable();
        $this->filterableColumns = property_exists($this->model, 'filterableColumns') ? $this->model->filterableColumns : null;
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
        $columnExists = function ($column): bool {
            if ($this->filterableColumns) return in_array($column, $this->filterableColumns);
            else return Schema::hasColumn($this->table, $column);
        };

        if (Str::contains($column, '->')) {
            $column = explode('->', $column)[0];
            return $columnExists($column) and $this->isColumnJson($column);
        }

        return $columnExists($column);
    }

    public function isColumnJson(string $column): bool
    {
        $cast = $this->model->getCasts()[$column] ?? null;
        if (in_array($cast, ['array', 'json'])) return true;

        return in_array(Schema::getColumnType($this->table, $column), ['json', 'text']);
    }

    public function getModelSettings(null|string $setting = null): null|array
    {
        $data = [];
        if ($configConstraint = config("laravel_query_filter.model_settings." . $this->model::class)) {
            if (is_callable($configConstraint)) {
                $data = $configConstraint();
            } elseif (is_array($configConstraint)) {
                $data = $configConstraint;
            } elseif (is_string($configConstraint)) {
                /**
                 * @var FilterSettingsInterface $instance
                 */
                $instance = new $configConstraint;
                $data = $instance->handle();
            }
        }

        return $setting ? ($data[$setting] ?? null) : $data;
    }

    public function getPrefix(): string
    {
        return $this->table ? $this->table . '.' : '';
    }
}
