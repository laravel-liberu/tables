<?php

namespace LaravelLiberu\Tables\Services\Data\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Contracts\ConditionalActions;
use LaravelLiberu\Tables\Contracts\CustomCssClasses;
use LaravelLiberu\Tables\Contracts\Table;
use LaravelLiberu\Tables\Services\Data\Config;
use LaravelLiberu\Tables\Services\Data\Filters;
use LaravelLiberu\Tables\Services\Data\Sorts\Sort;

class Data
{
    private readonly Builder $query;
    private Collection $data;

    public function __construct(
        private readonly Table $table,
        private readonly Config $config,
        private readonly bool $fetchMode = false
    ) {
        $this->query = $table->query();
    }

    public function handle(): Collection
    {
        $this->filter()
            ->sort()
            ->limit()
            ->setData();

        if ($this->data->isNotEmpty()) {
            $this->data = (new Computor($this->config, $this->data))->handle();

            if (! $this->fetchMode) {
                $this->actions();
                $this->style();
            }
        }

        return $this->data;
    }

    public function toArray(): array
    {
        return ['data' => $this->handle()];
    }

    private function filter(): self
    {
        (new Filters($this->table, $this->config, $this->query))->handle();

        return $this;
    }

    private function sort(): self
    {
        (new Sort($this->config, $this->query))->handle();

        return $this;
    }

    private function limit(): self
    {
        $this->query->skip($this->config->meta()->get('start'))
            ->take($this->config->meta()->get('length'));

        return $this;
    }

    private function setData(): self
    {
        $this->data = $this->query->get();

        return $this;
    }

    private function actions(): void
    {
        if ($this->table instanceof ConditionalActions) {
            $this->data->transform(fn ($row) => $row + [
                '_actions' => $this->rowActions($row),
            ]);
        }
    }

    private function style(): void
    {
        if ($this->table instanceof CustomCssClasses) {
            $this->data->transform(fn ($row) => $row + [
                '_cssClasses' => $this->table->cssClasses($row),
            ]);
        }
    }

    private function rowActions(array $row): array
    {
        return $this->config->template()->buttons()->get('row')
            ->map(fn (Obj $action) => $action->get('name'))
            ->filter(fn (string $action) => $this->table->render($row, $action))
            ->values()
            ->toArray();
    }
}
