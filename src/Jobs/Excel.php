<?php

namespace LaravelLiberu\Tables\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config as ConfigFacade;
use LaravelLiberu\Tables\Exports\Excel as Service;
use LaravelLiberu\Tables\Services\Data\Config;

class Excel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout;

    public function __construct(
        protected User $user,
        protected Config $config,
        protected string $table
    ) {
        $this->timeout = ConfigFacade::get('liberu.tables.export.timeout');
        $this->queue = ConfigFacade::get('liberu.tables.queues.exports');
    }

    public function handle()
    {
        (new Service($this->user, $this->table(), $this->config))->handle();
    }

    protected function table()
    {
        return App::make($this->table, ['request' => $this->config->request()]);
    }
}
