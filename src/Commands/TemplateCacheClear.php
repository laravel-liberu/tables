<?php

namespace LaravelLiberu\Tables\Commands;

use Illuminate\Cache\TaggableStore;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class TemplateCacheClear extends Command
{
    protected $signature = 'liberu:tables:clear';

    protected $description = 'Clear cached table templates';

    public function handle()
    {
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(Config::get('liberu.tables.cache.tag'))->flush();
            $this->info('Liberu table cached templates cleared');
        } elseif ($this->confirm("Your cache driver doesn't support tags, therefore we should flush the whole cache")) {
            Cache::flush();
            $this->info('Application cache cleared');
        } else {
            $this->warn('Liberu Table cached templates were not cleared');
        }
    }
}
