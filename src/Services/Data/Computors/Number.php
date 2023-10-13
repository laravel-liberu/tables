<?php

namespace LaravelLiberu\Tables\Services\Data\Computors;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Contracts\ComputesArrayColumns;
use NumberFormatter as Formatter;

class Number implements ComputesArrayColumns
{
    private static Obj $columns;
    private static Formatter $formatter;

    public static function columns($columns): void
    {
        self::$columns = $columns
            ->filter(fn ($column) => $column->has('number'))
            ->values();
    }

    public static function handle(array $row): array
    {
        foreach (self::$columns as $column) {
            Arr::set($row, $column->get('name'), self::format(
                Arr::get($row, $column->get('name')),
                $column->get('number')->get('precision')
            ));
        }

        return $row;
    }

    public static function format($value, $precision = 0)
    {
        if (! isset(self::$formatter)) {
            self::$formatter = new Formatter(App::getLocale(), Formatter::DECIMAL);
        }

        self::$formatter->setAttribute(Formatter::FRACTION_DIGITS, $precision);

        return self::$formatter->format($value);
    }
}
