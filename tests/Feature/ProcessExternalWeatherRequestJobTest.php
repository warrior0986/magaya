<?php

namespace Tests\Feature;

use App\Jobs\ProcessExternalWeatherRequest;
use App\Models\User;
use App\Models\WeatherRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProcessExternalWeatherRequestJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_job_update_weather_request_correctly()
    {
        Http::fake([
            'https://weatherdbi.herokuapp.com/data/weather/*' => Http::response([
                'region' => fake()->sentence(3),
                'currentConditions' => [
                    "dayhour" => fake()->date('D h'),
                    "temp" => [
                        "c" => fake()->numberBetween(0,50),
                        "f" => fake()->numberBetween(0,100)
                    ],
                    "precip" => fake()->numberBetween(0, 100) . "%",
                    "humidity" => fake()->numberBetween(0, 100) . "%",
                    "wind" => [
                        "km" => fake()->numberBetween(0, 100),
                        "mile" => fake()->numberBetween(0, 100),
                    ],
                    "iconURL" => fake()->url()
                ]
            ])
        ]);

        $user = User::factory()->create();
        $weatherRequest = WeatherRequest::factory()->withoutExternal($user->id)->create();
        $city = 'AnyCity';
        $job = new ProcessExternalWeatherRequest($weatherRequest, $city);
        $job->handle();

        $weatherRequest->refresh();
        $this->assertDatabaseHas('weather_requests', [
            'region_name' => $weatherRequest['region_name'],
            'current_conditions' => json_encode($weatherRequest['current_conditions']),
        ]);
    }

    /** @test */
    public function test_job_update_weather_request_correctly_when_fails()
    {
        Http::fake([
            'https://weatherdbi.herokuapp.com/data/weather/*' => Http::response([
                "status" => "fail",
                "message" => "invalid query",
                "query" => fake()->word(),
                "code" => 0,
                "visit" => "https://weatherdbi.herokuapp.com/documentation/v1"
            ])
        ]);

        $user = User::factory()->create();
        $weatherRequest = WeatherRequest::factory()->withoutExternal($user->id)->create();
        $city = 'AnyCity';
        $job = new ProcessExternalWeatherRequest($weatherRequest, $city);
        $job->handle();

        $weatherRequest->refresh();
        $this->assertDatabaseHas('weather_requests', [
            'status' => 'fail'
        ]);
    }

    /** @test */
    /*public function throw_not_found_when_not_users()
    {
        $response = $this->get(route('token'));
        $response->assertStatus(404);
    }*/
}
