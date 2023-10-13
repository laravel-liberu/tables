<?php

namespace LaravelLiberu\Tables\Services\Data\Computors;

use Illuminate\Database\Eloquent\Model;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Contracts\ComputesModelColumns;

class Method implements ComputesModelColumns
{
    private static Obj $columns;

    public static function columns($columns): void
    {
        self::$columns = $columns
            ->filter(fn ($column) => $column->has('meta')
                && $column->get('meta')->get('method'))
            ->values();
    }

    public static function handle(Model $row)
    {
        foreach (self::$columns as $column) {
            $row->{$column->get('name')} = $row->{$column->get('name')}();
        }

        return $row;
    }
}
