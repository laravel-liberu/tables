<?php

namespace LaravelLiberu\Tables\Services\Data\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Contracts\RawTotal;
use LaravelLiberu\Tables\Contracts\Table;
use LaravelLiberu\Tables\Exceptions\Meta as Exception;
use LaravelLiberu\Tables\Services\Data\Computors\Number;
use LaravelLiberu\Tables\Services\Data\Config;

class Total
{
    private array $total;

    public function __construct(
        private readonly Table $table,
        private readonly Config $config,
        private readonly Builder $query
    ) {
        $this->total = [];
    }

    public function handle(): array
    {
        $this->config->columns()
            ->filter(fn ($column) => $column->get('meta')->get('total')
                || $column->get('meta')->get('rawTotal')
                || $column->get('meta')->get('average'))
            ->each(fn ($column) => $this->compute($column));

        return $this->total;
    }

    private function compute(Obj $column): void
    {
        if ($column->get('meta')->get('rawTotal')) {
            $this->total[$column->get('name')] = $this->rawTotal($column);
        } elseif ($column->get('meta')->get('average')) {
            $this->total[$column->get('name')] = $this->query->average($column->get('data'));
        } else {
            $this->total[$column->get('name')] = $this->query->sum($column->get('data'));
        }

        if ($column->get('meta')->get('cents')) {
            $this->total[$column->get('name')] /= 100;
        }

        if ($column->has('number')) {
            $this->total[$column->get('name')] = Number::format(
                $this->total[$column->get('name')],
                $column->get('number')->get('precision')
            );
        }
    }

    private function rawTotal($column): string
    {
        if (! $this->table instanceof RawTotal) {
            throw Exception::missingInterface();
        }

        $rawTotal = $this->table->rawTotal($column);

        if (is_numeric($rawTotal)) {
            return $rawTotal;
        }

        $raw = DB::raw("{$rawTotal} as {$column->get('name')}");

        $result = $this->query->getQuery()->cloneWithoutBindings(['select'])
            ->select($raw)->first();

        return $result?->{$column->get('name')} ?? 0;
    }
}
