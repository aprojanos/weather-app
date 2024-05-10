<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WeatherPushSubscription;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Notifications\Notifiable;

class WeatherAlert extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'weather_push_subscription_id',
        'location',
        'coordinates',
        'alert_type',
        'temperature'
    ];

    use HasFactory;

    public function subcription() {
        return $this->hasOne(WeatherPushSubscription::class, 'subscribable_id');
    }
}
