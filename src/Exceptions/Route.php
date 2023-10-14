<?php

namespace LaravelLiberu\Tables\Exceptions;

use LaravelLiberu\Helpers\Exceptions\LiberuException;

class Route extends LiberuException
{
    public static function notFound(string $route)
    {
        return new static(__(
            'Read route does not exist: ":route"',
            ['route' => $route]
        ));
    }
}
