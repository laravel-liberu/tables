<?php

namespace LaravelLiberu\Tables\Services\Data\Builders;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use LaravelLiberu\Tables\Services\Data\ArrayComputors;
use LaravelLiberu\Tables\Services\Data\Config;
use LaravelLiberu\Tables\Services\Data\ModelComputors;

class Computor
{
    public function __construct(
        private readonly Config $config,
        private Collection $data,
    ) {
    }

    public function handle(): Collection
    {
        $this->appends()
            ->modelCompute()
            ->sanitize()
            ->arrayCompute()
            ->strip()
            ->flatten();

        return $this->data;
    }

    private function appends(): self
    {
        if ($this->config->filled('appends')) {
            $this->data->each->setAppends(
                $this->config->get('appends')->toArray()
            );
        }

        return $this;
    }

    private function modelCompute(): self
    {
        ModelComputors::handle($this->config, $this->data);

        return $this;
    }

    private function sanitize(): self
    {
        $this->data = new Collection($this->data->toArray());

        return $this;
    }

    private function arrayCompute(): self
    {
        ArrayComputors::handle($this->config, $this->data);

        return $this;
    }

    private function strip(): self
    {
        if (! $this->config->filled('strip')) {
            return $this;
        }

        $this->data->transform(function ($row) {
            foreach ($this->config->get('strip')->toArray() as $attr) {
                unset($row[$attr]);
            }

            return $row;
        });

        return $this;
    }

    private function flatten(): void
    {
        if ($this->config->get('flatten')) {
            $this->data->transform(fn ($record) => Arr::dot($record));
        }
    }
}
