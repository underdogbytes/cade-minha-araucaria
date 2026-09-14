<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class PasswordInputComponentTest extends TestCase
{
    public function test_password_input_component_renders_correctly(): void
    {
        $rendered = Blade::render('<x-utils.password-input id="password" name="password" required autocomplete="current-password" />');

        $this->assertStringContainsString('x-data="{ show: false }"', $rendered);
        $this->assertStringContainsString(':type="show ? \'text\' : \'password\'"', $rendered);
        $this->assertStringContainsString('id="password"', $rendered);
        $this->assertStringContainsString('name="password"', $rendered);
        $this->assertStringContainsString('required', $rendered);
        $this->assertStringContainsString('autocomplete="current-password"', $rendered);
        $this->assertStringContainsString('@click="show = !show"', $rendered);
        $this->assertStringContainsString('x-show="!show"', $rendered);
        $this->assertStringContainsString('x-show="show"', $rendered);
        $this->assertStringContainsString('x-cloak', $rendered);
    }

    public function test_input_password_and_password_aliases_render_correctly(): void
    {
        $renderedAlias1 = Blade::render('<x-utils.input-password id="pass1" name="pass1" />');
        $this->assertStringContainsString('id="pass1"', $renderedAlias1);
        $this->assertStringContainsString('x-data="{ show: false }"', $renderedAlias1);

        $renderedAlias2 = Blade::render('<x-utils.password id="pass2" name="pass2" />');
        $this->assertStringContainsString('id="pass2"', $renderedAlias2);
        $this->assertStringContainsString('x-data="{ show: false }"', $renderedAlias2);
    }

    public function test_layout_classes_are_passed_to_container(): void
    {
        $rendered = Blade::render('<x-utils.password-input class="mt-2 block w-full" />');

        $this->assertStringContainsString('class="relative  mt-2 block w-full "', $rendered);
    }
}
