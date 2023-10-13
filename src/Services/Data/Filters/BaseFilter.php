<?php

namespace LaravelLiberu\Tables\Services\Data\Filters;

use Illuminate\Database\Eloquent\Builder;
use LaravelLiberu\Tables\Contracts\Filter;
use LaravelLiberu\Tables\Contracts\Table;
use LaravelLiberu\Tables\Services\Data\Config;

abstract class BaseFilter implements Filter
{
    public function __construct(
        protected Table $table,
        protected Config $config,
        protected Builder $query
    ) {
    }

    abstract public function applies(): bool;

    abstract public function handle(): void;
}
