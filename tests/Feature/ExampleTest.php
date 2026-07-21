<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_portfolio_page_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('David')
            ->assertSee('IT Support &amp; Web Developer', false)
            ->assertSee('David Sales')
            ->assertSee('Kontribusi saya')
            ->assertSee('Home Server &amp; Private Cloud', false);
    }

    public function test_missing_personal_assets_use_safe_fallbacks(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Tentang saya')
            ->assertSee('DV')
            ->assertDontSee('Unduh CV')
            ->assertDontSee('images/profile/david.webp');
    }
}
