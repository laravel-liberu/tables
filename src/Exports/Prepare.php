<?php

namespace LaravelLiberu\Tables\Exports;

use Illuminate\Foundation\Auth\User;
use LaravelLiberu\DataExport\Enums\Statuses;
use LaravelLiberu\DataExport\Models\Export;
use LaravelLiberu\Tables\Jobs\EnsoExcel;
use LaravelLiberu\Tables\Jobs\Excel;
use LaravelLiberu\Tables\Services\Data\Config;

class Prepare
{
    public function __construct(
        protected User $user,
        protected Config $config,
        protected string $table
    ) {
    }

    public function handle(): void
    {
        $args = [$this->user, $this->config, $this->table];

        if ($this->config->isEnso()) {
            $args[] = $this->export();
            EnsoExcel::dispatch(...$args);
        } else {
            Excel::dispatch(...$args);
        }
    }

    protected function export(): Export
    {
        return Export::factory()->create([
            'name' => $this->config->name(),
            'status' => Statuses::Waiting,
        ]);
    }
}
