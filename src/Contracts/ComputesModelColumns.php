<?php

namespace LaravelLiberu\Tables\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ComputesModelColumns extends ComputesColumns
{
    public static function handle(Model $row);
}
