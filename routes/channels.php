<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * Define the broadcast channel for the weather-channel.
 *
 * @param  \Illuminate\Support\Collection|null  $user
 * @return bool
 */
Broadcast::channel('weather-channel', function ($user) {
    return true;
});