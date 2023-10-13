<?php

namespace LaravelLiberu\Tables\Services\Template;

use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Contracts\Table;
use LaravelLiberu\Tables\Services\Template\Validators\Buttons\Buttons;
use LaravelLiberu\Tables\Services\Template\Validators\Columns\Columns;
use LaravelLiberu\Tables\Services\Template\Validators\Controls;
use LaravelLiberu\Tables\Services\Template\Validators\Filters\Filters;
use LaravelLiberu\Tables\Services\Template\Validators\Route;
use LaravelLiberu\Tables\Services\Template\Validators\Structure\Attributes;
use LaravelLiberu\Tables\Services\Template\Validators\Structure\Structure;

class Validator
{
    public function __construct(
        private readonly Obj $template,
        private readonly Table $table
    ) {
    }

    public function run()
    {
        (new Structure($this->template))->validate();

        (new Attributes($this->template))->validate();

        (new Route($this->template))->validate();

        (new Buttons($this->template, $this->table))->validate();

        (new Filters($this->template))->validate();

        (new Controls($this->template))->validate();

        (new Columns($this->template))->validate();
    }
}
