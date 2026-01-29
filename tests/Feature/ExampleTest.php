<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test */
    public function check_env()
    {
        $this->assertEquals('testing', app()->environment());
    }

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')
            ->assertStatus(302)
            ->assertRedirect('/login');
    }
}
