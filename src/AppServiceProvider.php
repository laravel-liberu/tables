<?php

namespace LaravelLiberu\Tables;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use LaravelLiberu\Tables\Commands\TemplateCacheClear;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->load()
            ->publish()
            ->commands(TemplateCacheClear::class);
    }

    private function load()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tables.php', 'liberu.tables');

        $this->mergeConfigFrom(__DIR__.'/../config/api.php', 'liberu.tables');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-liberu/tables');

        return $this;
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/../config/tables.php' => config_path('liberu/tables.php'),
        ], ['tables-config', 'liberu-config']);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-liberu/tables'),
        ], ['tables-mail', 'liberu-mail']);

        $this->stubs()->each(fn ($ext, $stub) => $this->publishes([
            __DIR__."/../stubs/{$stub}.stub" => app_path("{$stub}.{$ext}"),
        ]));

        return $this;
    }

    private function stubs()
    {
        return new Collection([
            'Tables/Actions/CustomAction' => 'php',
            'Tables/Builders/ModelTable' => 'php',
            'Tables/Templates/template' => 'json',
        ]);
    }
}
