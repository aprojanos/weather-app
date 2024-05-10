<?php

namespace App\Services;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Notifications\WeatherAlertNotification;
use App\Models\User;
use App\Models\WeatherAlert;

/**
 * Class WeatherService.
 */
class WeatherService
/**
 * Provides methods for interacting with a weather API, including searching for locations, fetching weather forecasts, and sending weather alert notifications to users.
 */
{
    protected static $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected static function getClient(): Client
    {

        if (static::$client == null) {
            static::$client = new Client();
        }

        return static::$client;
    }

    public static function search(string $q)
    {
        try {
            $response = self::getClient()->get(env('WEATHER_API_URL') . 'search.json', [
                'query' => [
                    'key' => env('WEATHER_API_KEY'),
                    'q' => "{$q}*",
                ],
            ]);
            return ['success' => true, 'locations' => json_decode($response->getBody())];
        } catch (ClientException $e) {
            return ['success' => false, 'message' => json_decode($e->getResponse()->getBody())->error];
        }
    }

    public static function forecast(string $location, string $lang = 'en')
    {

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

        return ['success' => true, 'forecast' => json_decode($response->getBody())];
    }

    public static function current(string $location, string $lang = 'en')
    {

        try {
            $response = self::getClient()->get(env('WEATHER_API_URL') . 'current.json', [
                'query' => [
                    'key' => env('WEATHER_API_KEY'),
                    'q' => $location,
                    'lang' => $lang,
                    'aqi' => 'yes',
                    'alerts' => 'no',
                ],
            ]);
        } catch (ClientException $e) {
            return ['success' => false, 'message' => json_decode($e->getResponse()->getBody())->error];
        }

        return ['success' => true, 'weather' => json_decode($response->getBody())];
    }

    public static function checkAlertNotifications()
    {
        foreach(WeatherAlert::all() as $alert) {
            $result = WeatherService::current($alert->location);
            if ($result['success']) {
                $temp_c = $result['weather']->current->temp_c;
                if ($alert->alert_type == 'above' && $temp_c > $alert->temperature
                    || $alert->alert_type == 'below' && $temp_c < $alert->temperature
                ) {
                    print "notify: $alert->id ($temp_c, $alert->location, $alert->alert_type, $alert->temperature)";
                    $alert->notify(new WeatherAlertNotification($temp_c, $alert->location, $alert->alert_type, $alert->temperature));
                }
            }
        }
    }


}
