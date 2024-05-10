<?php

namespace App\Models;

use NotificationChannels\WebPush\PushSubscription;
use App\Models\WeatherAlert;

class WeatherPushSubscription extends PushSubscription {

    public function alert() {
        return $this->belongsTo(WeatherAlert::class, 'subscription_id');
    }

}
