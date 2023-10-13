<?php

namespace LaravelLiberu\Tables\Traits;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

trait TableCache
{
    public static function bootTableCache()
    {
        self::created(fn ($model) => $model->resetTableCache());

        self::deleted(fn ($model) => $model->resetTableCache());
    }

    public function resetTableCache()
    {
        $key = $this->tableCacheKey();

        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags($key)->flush();
        } else {
            Cache::forget($key);
        }
    }

    public function tableCacheKey(): string
    {
        $prefix = Config::get('liberu.tables.cache.prefix');

        return "{$prefix}:{$this->getTable()}";
    }
}
