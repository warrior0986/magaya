<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WeatherRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeatherRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WeatherRequest::factory()->user(User::first()->id)->times(20)->create();
    }
}
