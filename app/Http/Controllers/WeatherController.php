<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class WeatherController extends Controller
{    

    public function forecast(string $location, $lang = 'en') {

        $result = WeatherService::forecast($location);

        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            return response()->json($result['message'], 400);
        }
        
    }
    public function search(string $q) {

        $result = WeatherService::search($q);

        if ($result['success']) {
            return response()->json($result['locations'], 200);
        } else {
            return response()->json($result['message'], 400);
        }
        
    }
}
