<?php

namespace LaravelLiberu\Tables\Exports;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config as ConfigFacade;
use LaravelLiberu\DataExport\Enums\Statuses;
use LaravelLiberu\DataExport\Models\Export;
use LaravelLiberu\DataExport\Notifications\ExportDone;
use LaravelLiberu\Files\Models\File;
use LaravelLiberu\Tables\Contracts\Table;
use LaravelLiberu\Tables\Services\Data\Config;

class LiberuExcel extends Excel
{
    public function __construct(
        protected User $user,
        protected Table $table,
        protected Config $config,
        private readonly Export $export
    ) {
    }

    protected function process(): void
    {
        App::setLocale($this->user->preferences()->global->lang);

        $this->export->update([
            'status' => Statuses::Processing,
            'total' => $this->count,
        ]);

        parent::process();
    }

    protected function updateProgress(int $chunkSize): self
    {
        parent::updateProgress($chunkSize);

        $this->export->update(['entries' => $this->entryCount]);
        $this->cancelled = $this->export->fresh()->cancelled();

        return $this;
    }

    protected function finalize(): void
    {
        $args = [$this->export, $this->savedName, $this->filename, $this->export->created_by];

        $file = File::attach(...$args);

        $this->export->fill(['status' => Statuses::Finalized])
            ->file()->associate($file)
            ->save();

        $notification = new ExportDone($this->export, $this->emailSubject());
        $queue = ConfigFacade::get('liberu.tables.queues.notifications');
        $this->user->notify($notification->onQueue($queue));
    }

    protected function notifyError(): void
    {
        $this->export->update(['status' => Statuses::Failed]);

        parent::notifyError();
    }

    private function emailSubject(): string
    {
        $name = $this->config->label();

        return __(':name export done', ['name' => $name]);
    }
}
