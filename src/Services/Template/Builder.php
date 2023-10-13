<?php

namespace LaravelLiberu\Tables\Services\Template;

use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Services\Template\Builders\Buttons;
use LaravelLiberu\Tables\Services\Template\Builders\Columns;
use LaravelLiberu\Tables\Services\Template\Builders\Controls;
use LaravelLiberu\Tables\Services\Template\Builders\Filters;
use LaravelLiberu\Tables\Services\Template\Builders\Structure;
use LaravelLiberu\Tables\Services\Template\Builders\Style;

class Builder
{
    public function __construct(
        private readonly Obj $template,
        private readonly Obj $meta
    ) {
    }

    public function handleCacheable()
    {
        (new Structure($this->template, $this->meta))->build();

        (new Columns($this->template, $this->meta))->build();

        (new Style($this->template))->build();

        (new Controls($this->template))->build();
    }

    public function handleNonCacheable()
    {
        (new Buttons($this->template))->build();

        (new Filters($this->template, $this->meta))->build();

        $this->template->forget(['dataRouteSuffix', 'routePrefix']);
    }
}
