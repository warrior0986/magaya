<?php

namespace Tests\Feature;

use App\Jobs\ProcessExternalWeatherRequest;
use App\Models\Comments;
use App\Models\User;
use App\Models\WeatherRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_see_all_his_comments()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs(
            $user1
        );
        $weatherRequest1 = WeatherRequest::factory()->user($user1->id)->hasComments(2)->create();
        WeatherRequest::factory()->user($user2->id)->hasComments(1)->create();
        $commentsIds = $weatherRequest1->comments->pluck('id')->toArray();

        $response = $this->getJson(route('weather-request.comment.index', ['weather_request' => $weatherRequest1->id]));
        $response->assertStatus(200);
        $response->assertSee($commentsIds);
        $responseContent = json_decode($response->getContent());

        $this->assertCount(2, $responseContent->data[0]->comments->data);
    }

    /** @test */
    public function authenticated_user_can_see_only_his_comments()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );
        $weatherRequest = WeatherRequest::factory()->user($user->id)->hasComments(2)->create();
        $commentsIds = $weatherRequest->comments->pluck('id')->toArray();

        $response = $this->getJson(route('weather-request.comment.index', ['weather_request' => $weatherRequest->id]));
        $response->assertStatus(200);
        $response->assertSee($commentsIds);
    }

    /** @test */
    public function authenticated_user_can_delete_one_of_his_comments()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );
        $weatherRequest = WeatherRequest::factory()->user($user->id)->hasComments(1)->create();
        $commentsIds = $weatherRequest->comments->pluck('id')->toArray();

        $response = $this->deleteJson(route(
            'weather-requests.comment.delete',
            [
                'weather_request' => $weatherRequest['id'],
                'comment' => $commentsIds[0],
            ]
        ));
        $response->assertStatus(204);
        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function throw_not_found_when_not_exists_comment_to_delete_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );
        $weatherRequest = WeatherRequest::factory()->user($user->id)->hasComments(2)->create();
        $commentsIds = $weatherRequest->comments->pluck('id')->toArray();
        do {
            $id = random_int(1,10);
        } while(in_array($id, array($commentsIds)));

        $response = $this->deleteJson(route(
            'weather-requests.comment.delete',
            [
                'weather_request' => $weatherRequest['id'],
                'comment' => $id,
            ]
        ));
        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_post_one_requests()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );

        $weatherRequest = WeatherRequest::factory()->user($user->id)->create()->toArray();

        $response = $this->postJson(
            route('weather-request.comment.store',
                [
                    'weather_request' => $weatherRequest['id'],
                    'body' => fake()->paragraph(),
                ]
            )
        );
        $response->assertStatus(201);
        $this->assertDatabaseCount('comments', 1);
    }

    /** @test */
    public function throw_not_found_when_weather_request_not_exists()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user
        );

        $weatherRequest = WeatherRequest::factory()->user($user->id)->create()->toArray();
        do {
            $id = random_int(1,10);
        } while(in_array($id, array($weatherRequest['id'])));

        $response = $this->postJson(
            route('weather-request.comment.store',
                [
                    'weather_request' => $id,
                    'body' => fake()->paragraph(),
                ]
            )
        );
        $response->assertStatus(404);
        $this->assertDatabaseCount('comments', 0);
    }


    /** @test */
    public function unauthenticated_user_can_not_see_comments()
    {
        $user = User::factory()->create();
        $weatherRequest = WeatherRequest::factory()->user($user->id)->create();

        $this->getJson(route('weather-request.comment.index', ['weather_request' => $weatherRequest->id]))->assertStatus(401);        
    }
}
