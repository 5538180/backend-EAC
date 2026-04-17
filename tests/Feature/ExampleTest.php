<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase

{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
      // - Metodo para pasar al siguiente test ->    $this->markTestSkipped('test por defecto'); 
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
