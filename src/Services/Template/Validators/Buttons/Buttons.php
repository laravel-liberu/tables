<?php

namespace LaravelLiberu\Tables\Services\Template\Validators\Buttons;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Contracts\Table;
use LaravelLiberu\Tables\Exceptions\Button as Exception;

class Buttons
{
    private const Validations = ['format', 'defaults', 'structure'];

    private readonly Obj $defaults;

    public function __construct(
        private readonly Obj $template,
        private readonly Table $table
    ) {
        $this->defaults = $this->configButtons();
    }

    public function validate(): void
    {
        Collection::wrap(self::Validations)
            ->each(fn ($validation) => $this->{$validation}());
    }

    private function format(): void
    {
        $invalid = $this->template->get('buttons')
            ->filter(fn ($button) => ! is_string($button) && ! $button instanceof Obj);

        if ($invalid->isNotEmpty()) {
            throw Exception::invalidFormat();
        }
    }

    private function defaults(): void
    {
        $diff = $this->template->get('buttons')
            ->filter(fn ($button) => is_string($button))
            ->diff($this->defaults->keys());

        if ($diff->isNotEmpty()) {
            throw Exception::undefined($diff->implode('", "'));
        }
    }

    private function structure(): void
    {
        $this->template->get('buttons')
            ->map(fn ($button) => $this->map($button))
            ->each(fn ($button) => (new Button($button, $this->table, $this->template))
                ->validate());
    }

    private function map($button)
    {
        return $button instanceof Obj
            ? $button
            : $this->defaults->get($button);
    }

    private function configButtons(): Obj
    {
        $global = (new Obj(Config::get('liberu.tables.buttons.global')))
            ->map(fn ($button, $key) => $button->set('type', 'global')
                ->set('name', $key));

        $row = (new Obj(Config::get('liberu.tables.buttons.row')))
            ->map(fn ($button, $key) => $button->set('type', 'row')
                ->set('name', $key));

        return $global->merge($row);
    }
}
