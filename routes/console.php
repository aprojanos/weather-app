<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Events\WeatherUpdateEvent;
use App\Services\WeatherService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/**
 * Schedule a weather update event to be dispatched every minute.
 *
 * @return void
 */
Schedule::call(function () {
    WeatherUpdateEvent::dispatch('reload');
})->everyMinute();


/**
 * Schedule a function to check for weather alert notifications every minute.
 *
 * @return void
 */
Schedule::call(function () {
    WeatherService::checkAlertNotifications();
})->everyMinute();
