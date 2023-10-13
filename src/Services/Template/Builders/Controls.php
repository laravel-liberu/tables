<?php

namespace LaravelLiberu\Tables\Services\Template\Builders;

use Illuminate\Support\Facades\Config;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Attributes\Controls as Attributes;

class Controls
{
    public function __construct(private readonly Obj $template)
    {
    }

    public function build(): void
    {
        if ($this->template->has('controls')) {
            return;
        }

        $controls = Config::get('liberu.tables.controls') ?? Attributes::List;
        $this->template->set('controls', $controls);
    }
}
