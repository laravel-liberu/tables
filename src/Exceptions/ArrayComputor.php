<?php

namespace LaravelLiberu\Tables\Exceptions;

use LaravelLiberu\Helpers\Exceptions\EnsoException;

class ArrayComputor extends EnsoException
{
    public static function missingInterface()
    {
        return new static(__(
            'Array computors must implement the "ComputesArrayColumns" interface'
        ));
    }
}
