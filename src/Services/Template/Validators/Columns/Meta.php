<?php

namespace LaravelLiberu\Tables\Services\Template\Validators\Columns;

use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Attributes\Column as Attributes;
use LaravelLiberu\Tables\Exceptions\Meta as Exception;

class Meta
{
    public static function validate(Obj $column)
    {
        $meta = $column->get('meta');

        $diff = $meta->diff(Attributes::Meta);

        if ($diff->isNotEmpty()) {
            throw Exception::unknownAttributes($diff->implode('", "'));
        }

        if ($meta->has('filterable') && $meta->has('icon')) {
            throw Exception::cannotFilterIcon($column->get('name'));
        }
    }
}
