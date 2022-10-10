<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeatherRequest>
 */
class WeatherRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::first()->id,
            'region_name' => fake()->sentence(3),
            'current_conditions' => [
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
                "iconURL" => fake()->url(),
            ]
        ];
    }

    public function user($userId)
    {
        return $this->state(function ($attributes) use($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }

    public function withoutExternal($userId)
    {
        return $this->state(function ($attributes) use($userId) {
            return [
                'user_id' => $userId,
                'region_name' => '',
                'current_conditions' => []
            ];
        });
    }
}
