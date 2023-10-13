<?php

namespace LaravelLiberu\Tables\Jobs;

use Illuminate\Foundation\Auth\User;
use LaravelLiberu\DataExport\Models\Export;
use LaravelLiberu\Tables\Exports\EnsoExcel as Service;
use LaravelLiberu\Tables\Services\Data\Config;

class EnsoExcel extends Excel
{
    private readonly Export $export;

    public function __construct(User $user, Config $config, string $table, Export $export)
    {
        parent::__construct($user, $config, $table);

        $this->export = $export;
    }

    public function handle()
    {
        $args = [$this->user, $this->table(), $this->config, $this->export];

        (new Service(...$args))->handle();
    }
}
