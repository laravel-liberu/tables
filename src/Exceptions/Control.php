<?php

namespace LaravelLiberu\Tables\Exceptions;

use LaravelLiberu\Helpers\Exceptions\LiberuException;

class Control extends LiberuException
{
    public static function invalidFormat()
    {
        return new static(__('The controls array may contain only strings'));
    }

    public static function undefined(string $controls)
    {
        return new static(__(
            'Unknown control(s) Found: ":controls"',
            ['controls' => $controls]
        ));
    }
}
