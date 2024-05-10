<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\SSEController;

Route::get('/', function () {
    return view('index');
});

Route::get('/weather/search/{q}', [WeatherController::class, 'search']);
Route::get('/weather/forecast/{location}/{lang?}', [WeatherController::class, 'forecast']);
Route::post('/weather/subscribe', [WeatherController::class, 'subscribe']);
Route::post('/weather/unsubscribe', [WeatherController::class, 'unsubscribe']);

// Route::get('/test', [WeatherController::class, 'test']);


Route::get('/sse', [SSEController::class, 'stream']);
