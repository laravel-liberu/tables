<?php

namespace LaravelLiberu\Tables\Services\Data\Computors;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Contracts\ComputesArrayColumns;

class Date implements ComputesArrayColumns
{
    private static Obj $columns;

    public static function columns($columns): void
    {
        self::$columns = $columns
            ->filter(fn ($column) => $column->get('meta')->get('date'))
            ->values();
    }

    public static function handle(array $row): array
    {
        foreach (self::$columns as $column) {
            $rowValue = Arr::get($row, $column->get('name'));
            if ($rowValue !== null) {
                Arr::set($row, $column->get('name'), Carbon::parse($rowValue)
                    ->setTimezone(Config::get('app.timezone'))
                    ->format(self::format($column)));
            }
        }

        return $row;
    }

    private static function format($column)
    {
        return $column->has('dateFormat')
            ? $column->get('dateFormat')
            : Config::get('liberu.tables.dateFormat');
    }
}
