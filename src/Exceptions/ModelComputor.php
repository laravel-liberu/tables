<?php

namespace LaravelLiberu\Tables\Exceptions;

use LaravelLiberu\Helpers\Exceptions\EnsoException;

class ModelComputor extends EnsoException
{
    public static function missingInterface()
    {
        return new static(__(
            'Model computors must implement the "ComputesModelColumns" interface'
        ));
    }
}
