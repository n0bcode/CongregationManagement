<?php

namespace Tests\Feature;

use App\View\Components\SmartForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class SmartFormPropertyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_smart_form_component()
    {
        $view = $this->blade(
            '<x-smart-form action="/test" method="POST">
                <input type="text" name="name" />
            </x-smart-form>'
        );

        $view->assertSee('form');
        $view->assertSee('action="/test"', false);
        $view->assertSee('method="POST"', false);
        $view->assertSee('x-data="smartForm"', false);
    }

    /** @test */
    public function it_renders_smart_form_with_files()
    {
        $view = $this->blade(
            '<x-smart-form action="/test" method="POST" has-files>
                <input type="file" name="file" />
            </x-smart-form>'
        );

        $view->assertSee('enctype="multipart/form-data"', false);
    }

    /** @test */
    public function it_renders_smart_form_with_put_method()
    {
        $view = $this->blade(
            '<x-smart-form action="/test" method="PUT">
                <input type="text" name="name" />
            </x-smart-form>'
        );

        $view->assertSee('<input type="hidden" name="_method" value="PUT">', false);
    }
}
