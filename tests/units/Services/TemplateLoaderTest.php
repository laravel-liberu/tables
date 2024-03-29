<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use LaravelLiberu\Tables\Services\Template;
use LaravelLiberu\Tables\Services\TemplateLoader;
use LaravelLiberu\Tables\Tests\units\Services\TestTable;
use Tests\TestCase;

class TemplateLoaderTest extends TestCase
{
    private $table;

    protected function setUp(): void
    {
        parent::setUp();

        Route::any('route')->name('testTables.tableData');
        Route::getRoutes()->refreshNameLookups();

        $this->table = new TestTable();

        Config::set('liberu.tables.cache.prefix', 'prefix');
        Config::set('liberu.tables.cache.tag', 'tag');
        Config::set('liberu.tables.cache.template', 'never');
    }

    /** @test */
    public function can_get_template()
    {
        $this->assertTemplate((new TemplateLoader($this->table))->handle());
    }

    /** @test */
    public function can_cache_template()
    {
        TestTable::cache('always');

        (new TemplateLoader($this->table))->handle();

        $cache = Cache::tags(['tag'])->get($this->cacheKey());

        $template = (new Template($this->table))->load($cache['template'], $cache['meta']);

        $this->assertTemplate($template);
    }

    /** @test */
    public function cannot_cache_template_with_never_cache_config()
    {
        Config::set('liberu.tables.cache.template', 'never');
        TestTable::cache(null);

        (new TemplateLoader($this->table))->handle();

        $this->assertNull(Cache::tags(['tag'])->get($this->cacheKey()));
    }

    /** @test */
    public function cannot_cache_template_with_never_template_cache()
    {
        Config::set('liberu.tables.cache.template', 'always');
        TestTable::cache('never');

        (new TemplateLoader($this->table))->handle();

        $this->assertNull(Cache::tags(['tag'])->get($this->cacheKey()));
    }

    /** @test */
    public function can_cache_with_environment()
    {
        Config::set('liberu.tables.cache.template', app()->environment());
        TestTable::cache(null);

        (new TemplateLoader($this->table))->handle();

        $cache = Cache::tags(['tag'])->get($this->cacheKey());

        $template = (new Template($this->table))->load($cache['template'], $cache['meta']);

        $this->assertTemplate($template);
    }

    private function assertTemplate($cache)
    {
        $this->assertEquals(
            'name',
            $cache->get('columns')->first()->get('name')
        );
    }

    private function cacheKey(): string
    {
        return Config::get('liberu.tables.cache.prefix')
            .':'.Str::slug(str_replace(
                ['/', '.'],
                [' ', ' '],
                $this->table->templatePath()
            ));
    }
}
