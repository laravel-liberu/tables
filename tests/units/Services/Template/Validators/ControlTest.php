<?php

namespace LaravelLiberu\Tables\Tests\units\Services\Template\Validators;

use Illuminate\Support\Facades\Route;
use LaravelLiberu\Helpers\Services\Obj;
use LaravelLiberu\Tables\Exceptions\Control as Exception;
use LaravelLiberu\Tables\Services\Template\Validators\Controls;
use Tests\TestCase;

class ControlTest extends TestCase
{
    private $validator;
    private $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->template = new Obj(['controls' => []]);
    }

    /** @test */
    public function cannot_validate_with_invalid_button_type()
    {
        $this->template->get('controls')->push('invalid_action');

        $this->expectException(Exception::class);

        $this->expectExceptionMessage(Exception::undefined('invalid_action')->getMessage());

        $this->validate();
    }

    /** @test */
    public function can_validate()
    {
        $this->createRoute();

        $this->template->get('controls')->push('columns');

        $this->validate();

        $this->assertTrue(true);
    }

    private function validate()
    {
        $this->validator = new Controls($this->template);

        $this->validator->validate();
    }

    private function createRoute()
    {
        Route::any('/test.button.create')->name('test.create');
        Route::getRoutes()->refreshNameLookups();

        return 'test.create';
    }
}
