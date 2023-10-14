<?php

namespace LaravelLiberu\Tables\Exceptions;

use LaravelLiberu\Helpers\Exceptions\LiberuException;

class Cache extends LiberuException
{
    public static function missingTrait(string $model)
    {
        return new static(__(
            'To cache the table count, model :model must use the "TableCache" trait',
            ['model' => $model]
        ));
    }
}
