<?php

namespace App\Models;

use NotificationChannels\WebPush\PushSubscription;
use App\Models\WeatherAlert;

class WeatherPushSubscription extends PushSubscription {

    /**
     * Get the related weather alert for this push subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function alert() {
        return $this->belongsTo(WeatherAlert::class, 'subscription_id');
    }

}
