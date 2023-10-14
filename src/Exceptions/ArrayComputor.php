<?php

namespace LaravelLiberu\Tables\Exceptions;

use LaravelLiberu\Helpers\Exceptions\LiberuException;

class ArrayComputor extends LiberuException
{
    public static function missingInterface()
    {
        return new static(__(
            'Array computors must implement the "ComputesArrayColumns" interface'
        ));
    }
}
