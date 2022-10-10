<?php

namespace Tests\Unit;

use App\Models\Comments;
use App\Models\User;
use App\Models\WeatherRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_comment_can_be_morphed_to_a_weather_request_model()
    {
        $user = User::factory()->create();
        $weatherRequest = WeatherRequest::factory()->user($user->id)->create();
        $comment = Comments::factory()->create([
          "commentable_id" => $weatherRequest->id,
          "commentable_type" => "App\Models\WeatherRequest",
        ]); 

        $this->assertInstanceOf(WeatherRequest::class, $comment->commentable);
    }
}