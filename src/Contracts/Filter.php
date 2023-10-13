<?php

namespace LaravelLiberu\Tables\Contracts;

interface Filter
{
    public function applies(): bool;

    public function handle();
}
