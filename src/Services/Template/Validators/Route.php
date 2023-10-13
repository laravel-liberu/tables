<?php

namespace LaravelLiberu\Tables\Services\Template\Validators;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route as Facade;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Exceptions\Route as Exception;

class Route
{
    private readonly string $readRoute;

    public function __construct(Obj $template)
    {
        $this->readRoute = $this->readRoute($template);
    }

    public function validate()
    {
        if (! Facade::has($this->readRoute)) {
            throw Exception::notFound($this->readRoute);
        }
    }

    private function readRoute(Obj $template)
    {
        $suffix = $template->has('dataRouteSuffix')
            ? $template->get('dataRouteSuffix')
            : Config::get('enso.tables.dataRouteSuffix');

        return "{$template->get('routePrefix')}.{$suffix}";
    }
}
