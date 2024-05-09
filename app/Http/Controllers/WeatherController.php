<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\WeatherService;
use App\Notifications\WeatherAlert;
use Illuminate\Support\Facades\DB;

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
    public function subscribe(Request $req)
    {
        $user = User::find(1);

        $user->updatePushSubscription(
            $req->post('endpoint'),
            $req->post('public_key'),
            $req->post('auth_token'),
            $req->post('encoding'),
        );
        
        DB::table('push_subscriptions')->where('endpoint', $req->post('endpoint'))->update([
            'location' => $req->post('location'),
            'coordinates' => $req->post('coordinates'),
            'alert_type' => $req->post('alert_type'),
            'temperature' => $req->post('threshold'),
        ]);

        return response()->json(['message' => 'Subscribed!']);
    }

    public function unsubscribe(Request $req)
    {
        $user = User::find(1);

        $user->deletePushSubscription($req->post('endpoint'));

        return response()->json(['message' => 'Unsubscribed!']);
    }

    public function send()
    {
        $user = User::find(1);
        $user->notify(new WeatherAlert());

        return redirect('/');
    }
}
