<?php

namespace LaravelEnso\Tables\App\Services\Template\Builders;

use Illuminate\Support\Collection;
use LaravelEnso\Helpers\App\Classes\Obj;
use LaravelEnso\Tables\App\Attributes\Column as Attributes;

class Columns
{
    private Obj $template;
    private Obj $meta;

    public function __construct(Obj $template, Obj $meta)
    {
        $this->template = $template;
        $this->meta = $meta;
    }

    public function build(): void
    {
        $columns = $this->template->get('columns')
            ->reduce(fn ($columns, $column) => $columns->push(
                $this->computeMeta($column)
                    ->computeDefaultSort($column)
                    ->updateDefaults($column)
            ), new Collection());

        $this->template->set('columns', $columns);
    }

    private function computeMeta($column): self
    {
        if (! $column->has('meta')) {
            $column->set('meta', new Obj());
        }

        $meta = collect(Attributes::Meta)
            ->reduce(fn ($meta, $attribute) => $meta
                ->set($attribute, $column->get('meta')->contains($attribute)), new Obj());

        $meta->set('visible', true);
        $meta->set('hidden', false);
        $column->set('meta', $meta);

        return $this;
    }

    private function computeDefaultSort($column): self
    {
        $meta = $column->get('meta');

        $defaultSort = $this->defaultSort($meta);

        $meta->set('sort', $defaultSort);

        $meta->forget(['sort:ASC', 'sort:DESC']);

        if ($defaultSort) {
            $this->meta->set('sort', true);
        }

        return $this;
    }

    private function defaultSort($meta): ?string
    {
        if ($meta->get('sort:ASC')) {
            return 'ASC';
        }

        if ($meta->get('sort:DESC')) {
            return 'DESC';
        }

        return $meta->get('sort');
    }

    private function updateDefaults($column): Obj
    {
        $meta = $column->get('meta');

        if ($meta->get('searchable')) {
            $this->meta->set('searchable', true);
        }

        if ($meta->get('total') || $meta->get('rawTotal') || $meta->get('customTotal')) {
            $this->meta->set('total', true);
        }

        if ($meta->get('date')) {
            $this->meta->set('date', true);
        }

        if ($meta->get('translatable')) {
            $this->meta->set('translatable', true);
        }

        if ($meta->get('cents')) {
            $this->meta->set('cents', true);
        }

        if ($column->has('enum')) {
            $this->meta->set('enum', true);
        }

        if ($column->has('money')) {
            $this->meta->set('money', true);
        }

        return $column;
    }
}
