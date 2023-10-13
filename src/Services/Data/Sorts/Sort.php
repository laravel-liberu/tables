<?php

namespace LaravelLiberu\Tables\Services\Data\Sorts;

use Illuminate\Database\Eloquent\Builder;
use LaravelLiberu\Tables\Services\Data\Config;

class Sort
{
    public function __construct(
        private readonly Config $config,
        private readonly Builder $query
    ) {
    }

    public function handle(): void
    {
        $sort = new CustomSort($this->config, $this->query);

        if ($sort->applies()) {
            $sort->handle();
        } elseif (! $this->query->getQuery()->orders) {
            $this->query->orderBy($this->config->template()->get('defaultSort'));
        }
    }
}
