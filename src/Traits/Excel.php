<?php

namespace LaravelLiberu\Tables\Traits;

use Illuminate\Http\Request;
use LaravelLiberu\Tables\Exports\Prepare;
use LaravelLiberu\Tables\Notifications\ExportStarted;

trait Excel
{
    use ProvidesData;

    public function __invoke(Request $request)
    {
        $tableClass = method_exists($this, 'tableClass')
            ? $this->tableClass($request)
            : $this->tableClass;

        $user = $request->user();
        ['config' => $config] = $this->data($request);
        $attrs = [$user, $config, $tableClass];

        $user->notifyNow(new ExportStarted($config->label()));

        (new Prepare(...$attrs))->handle();
    }
}
