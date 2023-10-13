<?php

namespace LaravelLiberu\Tables\Contracts;

use LaravelLiberu\Helpers\Services\Obj;

interface RawTotal
{
    public function rawTotal(Obj $column): string;
}
