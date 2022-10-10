<?php

namespace Tests\Feature;

use App\Jobs\ProcessExternalWeatherRequest;
use App\Models\User;
use App\Models\WeatherRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WeatherRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_see_all_his_requests()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );
        $weatherRequest = WeatherRequest::factory()->user($user->id)->create()->toArray();

        $response = $this->getJson(route('weather-requests'));
        $response->assertStatus(200);
        $response->assertSee($weatherRequest['region_name']);
    }

    /** @test */
    public function authenticated_user_can_see_only_his_requests()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs(
            $user1
        );
        WeatherRequest::factory()->user($user1->id)->times(2)->create()->toArray();
        WeatherRequest::factory()->user($user2->id)->create()->toArray();

        $response = $this->getJson(route('weather-requests'));
        $response->assertStatus(200);
        $responseContent = json_decode($response->getContent());

        $this->assertCount(2, $responseContent->data);
    }

    /** @test */
    public function authenticated_user_can_see_one_of_his_requests()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );
        $weatherRequest = WeatherRequest::factory()->user($user->id)->create()->toArray();

        $response = $this->getJson(route('weather-requests.show', ['id' => $weatherRequest['id']]));
        $response->assertStatus(200);
        $response->assertSee($weatherRequest['region_name']);
    }

    /** @test */
    public function throw_not_found_when_not_exists_weather_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );
        $weatherRequest = WeatherRequest::factory()->user($user->id)->create()->toArray();
        do {
            $id = random_int(1,10);
        } while(in_array($id, array($weatherRequest['id'])));

        $response = $this->getJson(route('weather-requests.show', ['id' => $id]));
        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_delete_one_of_his_requests()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );
        $weatherRequest = WeatherRequest::factory()->user($user->id)->create()->toArray();

        $response = $this->deleteJson(route('weather-requests.delete', ['id' => $weatherRequest['id']]));
        $response->assertStatus(204);
        $this->assertSoftDeleted('weather_requests', [
            'id' => $weatherRequest['id'],
        ]);
    }

    /** @test */
    public function throw_not_found_when_not_exists_weather_to_delete_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );
        $weatherRequest = WeatherRequest::factory()->user($user->id)->create()->toArray();
        do {
            $id = random_int(1,10);
        } while(in_array($id, array($weatherRequest['id'])));

        $response = $this->deleteJson(route('weather-requests.delete', ['id' => $id]));
        $response->assertStatus(404);
    }

    /** @test */
    public function throw_error_when_missing_city_parameter()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );
        //$weatherRequest = WeatherRequest::factory()->user($user->id)->create()->toArray();

        $response = $this->postJson(route('weather-requests.store'));
        $response->assertInvalid([
            "city_name" => [
                "The city name field is required."
                ]
            ]
        );
    }

    /** @test */
    public function authenticated_user_can_post_one_requests()
    {
        Queue::fake();
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );

        $response = $this->postJson(route('weather-requests.store'), ['city_name' => 'testCity']);
        $response->assertStatus(201);
        $this->assertDatabaseCount('weather_requests', 1);
        Queue::assertPushed(ProcessExternalWeatherRequest::class);
    }


    /** @test */
    public function unauthenticated_user_can_not_see_all_his_requests()
    {
        $user = User::factory()->create();
        WeatherRequest::factory()->user($user->id)->times(1)->create();

        $this->getJson(route('weather-requests'))->assertStatus(401);        
    }
}
