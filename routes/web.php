<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\SSEController;

Route::get('/', function () {
    return view('index');
});

Route::get('/weather/search/{q}', [WeatherController::class, 'search']);
Route::get('/weather/forecast/{location}/{lang?}', [WeatherController::class, 'forecast']);

Route::get('/sse', [SSEController::class, 'stream']);