<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Events\WeatherUpdateEvent;
use App\Services\WeatherService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    WeatherUpdateEvent::dispatch('reload');
})->everyFifteenMinutes();

Schedule::call(function () {
    WeatherService::checkAlertNotifications();
})->everyMinute();
