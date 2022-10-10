<?php

namespace App\Repositories;

use App\Jobs\ProcessExternalWeatherRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalWeatherRequestRepository implements ExternalWeatherRequestRepositoryInterface
{
    public function get($weatherRequest, $cityName)
    {
        ProcessExternalWeatherRequest::dispatch($weatherRequest, $cityName);
    }
}