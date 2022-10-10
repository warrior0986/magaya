<?php

namespace App\Jobs;

use App\Models\WeatherRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessExternalWeatherRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $weatherRequest;
    public $cityName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WeatherRequest $weatherRequest, string $cityName)
    {
        $this->weatherRequest = $weatherRequest;
        $this->cityName = $cityName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = config('app.external_weather_request_url');
        
        $request = Http::get($url . $this->cityName);
        $response = json_decode($request->body());
        if (!isset($response->status)) {
            $this->weatherRequest->update([
                'region_name' => $response->region,
                'current_conditions' => $response->currentConditions,
            ]);
        } else {
            $this->weatherRequest->update([
                'status' => 'fail'
            ]);
        }
    }
}
