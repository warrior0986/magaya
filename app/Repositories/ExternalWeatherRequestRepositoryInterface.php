<?php

namespace App\Repositories;

interface ExternalWeatherRequestRepositoryInterface
{
    public function get($weatherRequest, $cityName);
}