<?php

namespace LaravelLiberu\Tables\Services\Template\Validators\Filters;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Attributes\Filter as Attributes;
use LaravelLiberu\Tables\Exceptions\Filter as Exception;

class Filter
{
    private const Validations = ['mandatory', 'optional', 'complementary', 'route'];

    public function __construct(private readonly Obj $filter)
    {
    }

    public function validate(): void
    {
        Collection::wrap(self::Validations)
            ->each(fn ($validation) => $this->{$validation}());
    }

    private function mandatory(): void
    {
        $missing = Collection::wrap(Attributes::Mandatory)
            ->diff($this->filter->keys())
            ->isNotEmpty();

        if ($missing) {
            throw Exception::missingAttributes();
        }
    }

    private function optional(): void
    {
        $unknown = $this->filter->keys()
            ->diff(Attributes::Mandatory)
            ->diff(Attributes::Optional)
            ->isNotEmpty();

        if ($unknown) {
            throw Exception::unknownAttributes();
        }
    }

    private function complementary(): void
    {
        if ($this->filter->get('type') === 'select' && ! $this->filter->has('route')) {
            throw Exception::missingRoute();
        }
    }

    private function route(): void
    {
        $route = $this->filter->get('route');

        if ($route !== null && ! Route::has($route)) {
            throw Exception::routeNotFound($route);
        }
    }
}
