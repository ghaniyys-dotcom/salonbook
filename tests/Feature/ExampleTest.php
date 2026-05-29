<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        // Seed minimal data for home page
        Service::factory()->create(['is_active' => true]);
        
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
