<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Events\WeatherUpdate;
use App\Services\WeatherService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    WeatherUpdate::dispatch('reload');
})->everyFifteenMinutes();

Schedule::call(function () {
    WeatherService::checkSubscriptions();
})->everyTenSeconds();