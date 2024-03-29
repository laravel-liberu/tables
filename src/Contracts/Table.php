<?php

namespace LaravelLiberu\Tables\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Table
{
    public function query(): Builder;

    public function templatePath(): string;
}
