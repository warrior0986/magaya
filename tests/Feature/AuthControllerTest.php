<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_get_token_return_200()
    {
        User::factory()->create();
        $response = $this->get(route('token'));
        $response->assertStatus(200);
    }

    /** @test */
    public function throw_not_found_when_not_users()
    {
        $response = $this->get(route('token'));
        $response->assertStatus(404);
    }
}
