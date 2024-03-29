<?php

namespace LaravelLiberu\Tables\Tests\units\Services;

use Faker\Factory;
use Illuminate\Support\Facades\Route;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Services\Data\Config;
use LaravelLiberu\Tables\Services\Data\FilterAggregator;
use LaravelLiberu\Tables\Services\Data\Request;
use LaravelLiberu\Tables\Services\Template;

trait SetUp
{
    private $faker;
    private $testModel;
    private $table;
    private $config;
    private $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        Route::any('route')->name('testTables.tableData');
        Route::getRoutes()->refreshNameLookups();

        TestModel::createTable();

        $this->testModel = $this->createTestModel();

        $columns = $internalFilters = $filters = $intervals = $params = [];

        $meta = ['length' => 10, 'search' => '', 'searchMode' => 'full'];
        $filters = [$internalFilters, $filters, $intervals, $params];

        $aggregator = new FilterAggregator(...$filters);

        $request = new Request($columns, $meta, $aggregator());

        $request->columns()->push(new Obj([
            'name' => 'name',
            'data' => 'name',
            'meta' => ['searchable' => true],
        ]));

        $this->table = new TestTable();

        $template = (new Template($this->table))->buildCacheable()
            ->buildNonCacheable();

        $this->config = new Config($request, $template);

        $this->query = $this->table->query();
    }

    protected function createTestModel($name = null)
    {
        return TestModel::create([
            'name' => $name ?? $this->faker->name,
            'is_active' => $this->faker->boolean,
            'price' => $this->faker->numberBetween(1000, 10000),
        ]);
    }
}
