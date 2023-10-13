<?php

namespace LaravelLiberu\Tables\Services\Data;

use Illuminate\Support\Collection;
use LaravelLiberu\Tables\Contracts\ComputesArrayColumns;
use LaravelLiberu\Tables\Exceptions\ArrayComputor;
use LaravelLiberu\Tables\Services\Data\Computors\Cents;
use LaravelLiberu\Tables\Services\Data\Computors\Date;
use LaravelLiberu\Tables\Services\Data\Computors\DateTime;
use LaravelLiberu\Tables\Services\Data\Computors\Enum;
use LaravelLiberu\Tables\Services\Data\Computors\Number;
use LaravelLiberu\Tables\Services\Data\Computors\Translator;

class ArrayComputors extends Computors
{
    private static bool $serverSide = false;

    protected static array $computors = [
        'cents' => Cents::class,
        'enum' => Enum::class,
        'date' => Date::class,
        'datetime' => DateTime::class,
        'number' => Number::class,
        'translatable' => Translator::class,
    ];

    public static function serverSide(): void
    {
        self::$serverSide = true;
    }

    protected static function computor($computor): ComputesArrayColumns
    {
        $computor = new static::$computors[$computor]();

        if (! $computor instanceof ComputesArrayColumns) {
            throw ArrayComputor::missingInterface();
        }

        return $computor;
    }

    protected static function applicable(Config $config): Collection
    {
        return parent::applicable($config)
            ->when(! self::$serverSide, fn ($computors) => $computors
                ->reject(fn ($computor) => in_array($computor, ['enum', 'translatable'])));
    }
}
