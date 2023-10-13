<?php

namespace LaravelLiberu\Tables\Services\Template\Validators\Columns;

use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Exceptions\Column as Exception;

class Columns
{
    private readonly Obj $columns;

    public function __construct(Obj $template)
    {
        $this->columns = $template->get('columns');
    }

    public function validate()
    {
        $this->format()
            ->columns();
    }

    private function columns()
    {
        $this->columns
            ->each(fn ($column) => (new Column($column))->validate());
    }

    private function format()
    {
        if ($this->invalidFormat() || $this->invalidChild()) {
            throw Exception::invalidFormat();
        }

        return $this;
    }

    private function invalidFormat()
    {
        return ! $this->columns instanceof Obj
            || $this->columns->isEmpty();
    }

    private function invalidChild()
    {
        return $this->columns
            ->some(fn ($column) => ! $column instanceof Obj);
    }
}
