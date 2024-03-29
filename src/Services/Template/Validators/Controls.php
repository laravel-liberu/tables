<?php

namespace LaravelLiberu\Tables\Services\Template\Validators;

use Illuminate\Support\Facades\Config;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Exceptions\Control as Exception;

class Controls
{
    private readonly ?Obj $controls;
    private readonly Obj $defaults;

    public function __construct(Obj $template)
    {
        $this->controls = $template->get('controls');
        $this->defaults = new Obj(Config::get('liberu.tables.controls'));
    }

    public function validate()
    {
        if ($this->controls !== null) {
            $this->format()
                ->defaults();
        }
    }

    private function format()
    {
        if ($this->invalidFormat()) {
            throw Exception::invalidFormat();
        }

        return $this;
    }

    private function invalidFormat()
    {
        return ! $this->controls instanceof Obj || $this->controls
            ->filter(fn ($control) => ! is_string($control))
            ->isNotEmpty();
    }

    private function defaults()
    {
        $diff = $this->controls->diff($this->defaults);

        if ($diff->isNotEmpty()) {
            throw Exception::undefined($diff->implode('", "'));
        }

        return $this;
    }
}
