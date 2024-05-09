<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Notifications\WeatherAlert;
use App\Models\User;
/**
 * Class WeatherService.
 */
class WeatherService
{
    protected static $client;

    protected static function getClient(): Client {

        if (static::$client == null) {
            static::$client = new Client();
        }

        return static::$client;
    }

    public function __construct(Client $client) {
        $this->client = $client;
    }

    public static function search(string $q) { 
        try {
            $response = self::getClient()->get(env('WEATHER_API_URL') . 'search.json', [
                'query' => [
                    'key' => env('WEATHER_API_KEY'),
                    'q' => "{$q}*",
                ],
            ]);
            return ['success'=>true, 'locations'=>json_decode($response->getBody())];
        } catch (ClientException $e) {
            return ['success' => false, 'message' => json_decode($e->getResponse()->getBody())->error];
        }

    }

    public static function forecast(string $location, string $lang = 'en') {   

        try {
            $response = self::getClient()->get(env('WEATHER_API_URL') . 'forecast.json', [
                'query' => [
                    'key' => env('WEATHER_API_KEY'),
                    'q' => $location,
                    'lang' => $lang,
                    'days' => 2,
                    // 'hour' => date('H'),
                    'aqi' => 'yes',
                    'alerts' => 'no',
                ],
            ]);
        } catch (ClientException $e) {
            return ['success' => false, 'message' => json_decode($e->getResponse()->getBody())->error];
        }

        return ['success'=>true, 'forecast'=>json_decode($response->getBody())];
    }

    public static function checkSubscriptions() {
        $user = User::find(1);
        $user->notify( new WeatherAlert('18', 'Budapest', 'above', 12));
    }

}
