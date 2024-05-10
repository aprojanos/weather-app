<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WeatherAlert;
use App\Models\WeatherPushSubscription;
use Illuminate\Http\Request;
use App\Services\WeatherService;
use Illuminate\Support\Facades\DB;

class WeatherController extends Controller {

    /**
     * Provides weather forecast information for a given location.
     *
     * @param string $location The location to retrieve the weather forecast for.
     * @param string $lang     The language to return the weather forecast in (default is 'en').
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the weather forecast information.
     */

    /**
     * Retrieves the weather forecast for the specified location.
     *
     * @param string $location The location to retrieve the weather forecast for.
     * @param string $lang The language to use for the weather forecast (default is 'en').
     * @return \Illuminate\Http\JsonResponse A JSON response containing the weather forecast.
     */
    public function forecast(string $location, $lang = 'en')
    {

        $result = WeatherService::forecast($location);

        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            return response()->json($result['message'], 400);
        }
    }

    /**
     * Searches for weather locations based on the provided query.
     *
     * @param string $q The search query.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the search results.
     */
    public function search(string $q)
    {
        $result = WeatherService::search($q);

        if ($result['success']) {
            return response()->json($result['locations'], 200);
        } else {
            return response()->json($result['message'], 400);
        }
    }

    /**
     * Subscribes a user to weather alerts for a specific location.
     *
     * @param \Illuminate\Http\Request $req The request containing the subscription details.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the subscription was successful.
     */
    public function subscribe(Request $req)
    {
        $alert = WeatherAlert::create([
          //  'weather_push_subscription_id' => $subscription->id,
            'location' => $req->post('location'),
            'coordinates' => $req->post('coordinates'),
            'alert_type' => $req->post('alert_type'),
            'temperature' => $req->post('threshold'),
        ]);

        $subscription = $alert->updatePushSubscription(
            $req->post('endpoint'),
            $req->post('public_key'),
            $req->post('auth_token'),
            $req->post('encoding'),
        );
        return response()->json(['message' => "Subscribed to weather alert: {$alert->location} -> {$alert->alert_type} {$alert->temperature} degrees"]);
    }

    /**
     * Unsubscribes a user from weather alerts for a specific location.
     *
     * @param \Illuminate\Http\Request $req The request containing the subscription details.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the unsubscription was successful.
     */
    public function unsubscribe(Request $req)
    {
        $user = User::find(1);
        $alert = WeatherAlert::with('subcription')->whereHas('subcription', function($q) use($req) {
            $q->where('endpoint', $req->post('endpoint'));
        })->first();
        $subscription = $alert->pushSubscriptions();
        $subscription = WeatherPushSubscription::where('endpoint', $req->post('endpoint'))->first();
        if ($subscription) {
            $alert->delete();
            $subscription->delete();
            return response()->json(['message' => 'Unsubscribed from weather alert']);
        }

        return response()->json(['message' => 'Not subscribed!']);

    }

    public function test() {
        WeatherService::checkAlertNotifications();
    }

}
