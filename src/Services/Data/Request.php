<?php

namespace LaravelLiberu\Tables\Services\Data;

use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Services\Data\RequestArgument as Argument;

class Request
{
    private readonly Obj $columns;
    private readonly Obj $meta;
    private readonly Obj $searches;
    private readonly Obj $filters;
    private readonly Obj $intervals;
    private readonly Obj $params;

    public function __construct($columns, $meta, FilterAggregator $aggregator)
    {
        $this->columns = new Obj(Argument::parse($columns));
        $this->meta = new Obj(Argument::parse($meta));
        $this->searches = $aggregator->searches();
        $this->filters = $aggregator->filters();
        $this->intervals = $aggregator->intervals();
        $this->params = $aggregator->params();
    }

    public function columns(): Obj
    {
        return $this->columns;
    }

    public function meta(): Obj
    {
        return $this->meta;
    }

    public function searches(): Obj
    {
        return $this->searches;
    }

    public function filters(): Obj
    {
        return $this->filters;
    }

    public function intervals(): Obj
    {
        return $this->intervals;
    }

    public function params(): Obj
    {
        return $this->params;
    }

    public function column(string $name): ?Obj
    {
        return $this->columns
            ->first(fn ($column) => $column->get('name') === $name);
    }
}
