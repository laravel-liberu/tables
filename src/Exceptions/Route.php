<?php

namespace LaravelLiberu\Tables\Exceptions;

use LaravelLiberu\Helpers\Exceptions\EnsoException;

class Route extends EnsoException
{
    public static function notFound(string $route)
    {
        return new static(__(
            'Read route does not exist: ":route"',
            ['route' => $route]
        ));
    }
}
