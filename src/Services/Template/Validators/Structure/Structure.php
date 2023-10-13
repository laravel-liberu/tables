<?php

namespace LaravelLiberu\Tables\Services\Template\Validators\Structure;

use Illuminate\Support\Collection;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Attributes\Structure as Attributes;
use LaravelLiberu\Tables\Exceptions\Template as Exception;

class Structure
{
    public function __construct(private readonly Obj $template)
    {
    }

    public function validate()
    {
        $this->mandatoryAttributes()
            ->optionalAttributes();
    }

    private function mandatoryAttributes()
    {
        $diff = Collection::wrap(Attributes::Mandatory)
            ->diff($this->template->keys());

        if ($diff->isNotEmpty()) {
            throw Exception::missingAttributes($diff->implode('", "'));
        }

        return $this;
    }

    private function optionalAttributes()
    {
        $attributes = Collection::wrap(Attributes::Mandatory)
            ->merge(Attributes::Optional);

        $diff = $this->template->keys()->diff($attributes);

        if ($diff->isNotEmpty()) {
            throw Exception::unknownAttributes($diff->implode('", "'));
        }

        return $this;
    }
}
