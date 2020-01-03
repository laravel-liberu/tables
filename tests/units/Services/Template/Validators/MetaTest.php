<?php

namespace LaravelEnso\Tables\Tests\units\Services\Template\Validators;

use Illuminate\Support\Collection;
use LaravelEnso\Helpers\App\Classes\Obj;
use LaravelEnso\Tables\App\Attributes\Column as Attributes;
use LaravelEnso\Tables\App\Exceptions\Meta as Exception;
use LaravelEnso\Tables\App\Services\Template\Validators\Columns;
use Tests\TestCase;

class MetaTest extends TestCase
{
    private $validator;
    private $template;

    protected function setUp() :void
    {
        parent::setUp();

        // $this->withoutExceptionHandling();

        $this->template = new Obj(['columns' => [$this->mockedColumn()]]);
    }

    /** @test */
    public function can_validate_meta()
    {
        $this->template->get('columns')->first()->set('meta', new Obj(['sortable']));

        $this->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function cannot_validate_meta_with_wrong_attributes()
    {
        $this->template->get('columns')->first()->set('meta', new Obj(['wrong_attribute']));

        $this->expectException(Exception::class);

        $this->expectExceptionMessage(Exception::unknownAttributes('wrong_attribute')->getMessage());

        $this->validate();
    }

    /** @test */
    public function cannot_validate_nested_column_with_sortable()
    {
        $this->template->get('columns')->push(new Obj([
            'label' => 'child',
            'name' => 'parent.child',
            'data' => 'parent.child',
            'meta' => ['sortable']
        ]));

        $this->expectException(Exception::class);

        $this->expectExceptionMessage(Exception::unsupported('parent.child')->getMessage());

        $this->validate();
    }


    private function mockedColumn()
    {
        return (new Collection(Attributes::Mandatory))
            ->flip()
            ->map(fn () => new Obj());
    }

    private function validate()
    {
        $this->validator = new Columns($this->template);

        $this->validator->validate();
    }
}
