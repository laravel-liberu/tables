<?php

namespace LaravelLiberu\Tables\Contracts;

interface ComputesArrayColumns extends ComputesColumns
{
    public static function handle(array $row): array;
}
