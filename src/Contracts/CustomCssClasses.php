<?php

namespace LaravelLiberu\Tables\Contracts;

interface CustomCssClasses
{
    public function cssClasses(array $row): array;
}
