<?php

namespace LaravelLiberu\Tables\Services\Data\Builders;

use Carbon\Carbon;
use Illuminate\Cache\TaggableStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config as ConfigFacade;
use LaravelLiberu\Tables\Contracts\CustomCount;
use LaravelLiberu\Tables\Contracts\CustomCountCacheKey;
use LaravelLiberu\Tables\Contracts\Table;
use LaravelLiberu\Tables\Exceptions\Cache as Exception;
use LaravelLiberu\Tables\Services\Data\Computors\Number;
use LaravelLiberu\Tables\Services\Data\Config;
use LaravelLiberu\Tables\Services\Data\Filters;
use ReflectionClass;

class Meta
{
    private readonly Builder $query;
    private bool $filters;
    private int $count;
    private int $filtered;
    private array $total;
    private bool $fullRecordInfo;

    public function __construct(
        private readonly Table $table,
        private readonly Config $config
    ) {
        $this->query = $table->query();
        $this->total = [];
        $this->filters = false;
    }

    public function build(): self
    {
        $this->setCount()
            ->filter()
            ->detailedInfo()
            ->countFiltered()
            ->total();

        return $this;
    }

    public function toArray(): array
    {
        $this->build();

        return [
            'count' => $this->count,
            'formattedCount' => Number::format($this->count),
            'filtered' => $this->filtered,
            'formattedFiltered' => Number::format($this->filtered),
            'total' => $this->total,
            'fullRecordInfo' => $this->fullRecordInfo,
            'filters' => $this->filters,
            'pagination' => $this->pagination()->toArray(),
        ];
    }

    public function count($filtered = false): int
    {
        if ($this->table instanceof CustomCount && ! $filtered) {
            return $this->table->count();
        }

        return $this->query
            ->applyScopes()
            ->getQuery()
            ->getCountForPagination();
    }

    public function filter(): self
    {
        $filters = new Filters($this->table, $this->config, $this->query);

        $this->filters = $filters->applies();

        if ($this->filters) {
            $filters->handle();
        }

        return $this;
    }

    private function setCount(): self
    {
        $this->filtered = $this->count = $this->cachedCount();

        return $this;
    }

    private function detailedInfo(): self
    {
        $this->fullRecordInfo = $this->config->meta()->get('forceInfo')
            || $this->count <= $this->config->meta()->get('fullInfoRecordLimit')
            || (! $this->filters && ! $this->config->meta()->get('total'));

        return $this;
    }

    private function countFiltered(): self
    {
        if ($this->filters && $this->fullRecordInfo) {
            $this->filtered = $this->count(true);
        }

        return $this;
    }

    private function total(): self
    {
        if ($this->fullRecordInfo && $this->config->meta()->get('total')) {
            $this->total = (new Total($this->table, $this->config, $this->query))
                ->handle();
        }

        return $this;
    }

    private function pagination(): Pagination
    {
        $args = [$this->config->meta(), $this->filtered, $this->fullRecordInfo];

        return new Pagination(...$args);
    }

    private function cachedCount(): int
    {
        if (! $this->shouldCache()) {
            return $this->count();
        }

        $cacheKey = $this->table instanceof CustomCountCacheKey
            ? $this->cacheKey($this->table->countCacheKey())
            : $this->cacheKey();

        if (! $this->cache($this->cacheKey())->has($cacheKey)) {
            $this->cache($this->cacheKey())
                ->put($cacheKey, $this->count(), Carbon::now()->addHour());
        }

        return $this->cache($this->cacheKey())->get($cacheKey);
    }

    private function cache(string $tag)
    {
        return Cache::getStore() instanceof TaggableStore
            ? Cache::tags($tag)
            : Cache::store();
    }

    private function cacheKey(?string $suffix = null): string
    {
        $model = $this->query->getModel();
        $key = (new $model())->tableCacheKey();

        return Collection::wrap([$key, $suffix])->filter()->implode(':');
    }

    private function shouldCache(): bool
    {
        $shouldCache = $this->config->has('countCache')
            ? $this->config->get('countCache')
            : ConfigFacade::get('liberu.tables.cache.count');

        if ($shouldCache) {
            $model = $this->query->getModel();

            $instance = (new ReflectionClass($model));

            $compatible = $instance->hasMethod('resetTableCache')
                && $instance->hasMethod('tableCacheKey');

            if (! $compatible) {
                throw Exception::missingTrait($model::class);
            }
        }

        return $shouldCache
            && (Cache::getStore() instanceof TaggableStore
                || ! $this->table instanceof CustomCountCacheKey);
    }
}
