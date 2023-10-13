<?php

namespace LaravelLiberu\Tables\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use LaravelLiberu\Tables\Services\Data\Config;
use LaravelLiberu\Tables\Services\TemplateLoader;

trait ProvidesData
{
    use ProvidesRequest;

    public function data(Request $request)
    {
        $tableClass = method_exists($this, 'tableClass')
            ? $this->tableClass($request)
            : $this->tableClass;

        $request = $this->request($request);
        $table = App::make($tableClass, ['request' => $request]);
        $template = (new TemplateLoader($table))->handle();
        $config = new Config($request, $template);

        return ['table' => $table, 'config' => $config];
    }
}
