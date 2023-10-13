<?php

namespace LaravelLiberu\Tables\Contracts;

use Illuminate\Database\Eloquent\Builder;
use LaravelLiberu\Helpers\Services\Obj;

interface CustomFilter
{
    public function filterApplies(Obj $params): bool;

    public function filter(Builder $query, Obj $params);
}
