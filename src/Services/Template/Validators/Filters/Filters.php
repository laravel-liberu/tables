<?php

namespace LaravelLiberu\Tables\Services\Template\Validators\Filters;

use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Exceptions\Filter as Exception;

class Filters
{
    private readonly ?Obj $filters;

    public function __construct(Obj $template)
    {
        $this->filters = $template->get('filters');
    }

    public function validate(): void
    {
        if ($this->filters) {
            $this->format()
                ->structure();
        }
    }

    private function format(): self
    {
        $invalid = $this->filters
            ->filter(fn ($filter) => ! is_string($filter) && ! $filter instanceof Obj);

        if ($invalid->isNotEmpty()) {
            throw Exception::invalidFormat();
        }

        return $this;
    }

    private function structure(): self
    {
        $this->filters->each(fn ($filter) => (new Filter($filter))->validate());

        return $this;
    }
}
