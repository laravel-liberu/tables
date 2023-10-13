<?php

namespace LaravelLiberu\Tables\Services\Data\Computors;

use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Contracts\ComputesArrayColumns;

class Enum implements ComputesArrayColumns
{
    private static Obj $columns;

    public static function columns($columns): void
    {
        self::$columns = $columns
            ->filter(fn ($column) => $column->get('enum'))
            ->values();
    }

    public static function handle(array $row): array
    {
        foreach (self::$columns as $column) {
            $row[$column->get('name')] = $column
                ->get('enum')[$row[$column->get('name')]] ?? null;
        }

        return $row;
    }
}
