<?php

namespace LaravelLiberu\Tables\Services;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use LaravelLiberu\Tables\Contracts\DynamicTemplate;
use LaravelLiberu\Tables\Contracts\Table;

class TemplateLoader
{
    private Template $template;
    private array $cache;

    public function __construct(private readonly Table $table)
    {
    }

    public function handle()
    {
        $this->load();

        return $this->template;
    }

    private function load()
    {
        $this->template = $this->fromCache() ?? $this->new();

        if ($this->shouldCache()) {
            $this->cache()->put($this->cacheKey(), $this->template->toArray());
        }

        $this->template->buildNonCacheable();
    }

    private function fromCache()
    {
        if (! $this->cache()->has($this->cacheKey())) {
            return;
        }

        $this->cache = $this->cache()->get($this->cacheKey());

        return (new Template($this->table))
            ->load($this->cache['template'], $this->cache['meta']);
    }

    private function new()
    {
        return (new Template($this->table))->buildCacheable();
    }

    private function shouldCache()
    {
        if (isset($this->cache)) {
            return false;
        }

        $type = $this->template->get(
            'templateCache',
            Config::get('liberu.tables.cache.template')
        );

        return match ($type) {
            'never' => false,
            'always' => true,
            default => app()->environment($type),
        };
    }

    private function cacheKey(): string
    {
        $configPrefix = Config::get('liberu.tables.cache.prefix');

        $prefix = $this->table instanceof DynamicTemplate
            ? "{$this->table->cachePrefix()}:"
            : null;

        return  Str::of($this->table->templatePath())
            ->replace(['/', '.'], [' ', ' '])
            ->slug()
            ->prepend("{$configPrefix}:{$prefix}");
    }

    private function cache()
    {
        return Cache::getStore() instanceof TaggableStore
            ? Cache::tags(Config::get('liberu.tables.cache.tag'))
            : Cache::store();
    }
}
