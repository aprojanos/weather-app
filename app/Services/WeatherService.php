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

    /**
     * WeatherService constructor.
     *
     * @param Client $client The GuzzleHttp Client instance for making HTTP requests to the weather API.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieves the GuzzleHttp Client instance for making HTTP requests to the weather API.
     *
     * @return Client The GuzzleHttp Client instance.
     */
    protected static function getClient(): Client
    {
        if (static::$client == null) {
            static::$client = new Client();
        }

        return static::$client;
    }

    /**
     * Searches for locations based on the provided query.
     *
     * @param string $q The query string to search for locations.
     *
     * @return array An associative array containing the following keys:
     *  - success: A boolean indicating whether the search was successful.
     *  - locations: An array of location objects returned by the weather API.
     *
     * @throws ClientException If an error occurs while making the HTTP request to the weather API.
     */
    public static function search(string $q): array {
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

    /**
     * Retrieves the weather forecast for a given location.
     *
     * @param string $location The location for which to retrieve the forecast.
     * @param string $lang The language code for the forecast data. Defaults to 'en' (English).
     *
     * @return array An associative array containing the following keys:
     *  - success: A boolean indicating whether the forecast retrieval was successful.
     *  - forecast: An array of forecast data returned by the weather API.
     *
     * @throws ClientException If an error occurs while making the HTTP request to the weather API.
     */
    public static function forecast(string $location, string $lang = 'en'): array {
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

    /**
     * Retrieves the current weather for a given location.
     *
     * @param string $location The location for which to retrieve the current weather.
     * @param string $lang The language code for the weather data. Defaults to 'en' (English).
     *
     * @return array An associative array containing the following keys:
     *  - success: A boolean indicating whether the current weather retrieval was successful.
     *  - weather: An array of current weather data returned by the weather API.
     *
     * @throws ClientException If an error occurs while making the HTTP request to the weather API.
     */
    public static function current(string $location, string $lang = 'en'): array {
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

    /**
     * Checks all the weather alerts and sends notifications if necessary.
     */
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
                } else {
                    print "notification not needed: $alert->id ($temp_c, $alert->location, $alert->alert_type, $alert->temperature)";
                }
            }
        }
    }


}
