<?php

namespace LaravelLiberu\Tables\Exceptions;

use LaravelLiberu\Helpers\Exceptions\LiberuException;

class ModelComputor extends LiberuException
{
    public static function missingInterface()
    {
        return new static(__(
            'Model computors must implement the "ComputesModelColumns" interface'
        ));
    }
}
