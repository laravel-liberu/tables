<?php

namespace LaravelLiberu\Tables\Services\Data;

use LaravelLiberu\Tables\Contracts\ComputesModelColumns;
use LaravelLiberu\Tables\Exceptions\ModelComputor;
use LaravelLiberu\Tables\Services\Data\Computors\Method;
use LaravelLiberu\Tables\Services\Data\Computors\Resource;

class ModelComputors extends Computors
{
    protected static array $computors = [
        'method' => Method::class,
        'resource' => Resource::class,
    ];

    protected static function computor($computor): ComputesModelColumns
    {
        $computor = new self::$computors[$computor]();

        if (! $computor instanceof ComputesModelColumns) {
            throw ModelComputor::missingInterface();
        }

        return $computor;
    }
}
