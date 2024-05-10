<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Notification;

class WeatherAlertNotification extends Notification
{
    use Queueable;

    protected $temperature;
    protected $location;
    protected $alert_type;
    protected $threshold;

    /**
     * Create a new notification instance.
     */
    public function __construct($temperature, $location, $alert_type, $threshold)
    {
        $this->temperature = $temperature;
        $this->location = $location;
        $this->alert_type = $alert_type;
        $this->threshold = $threshold;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the push representation of the notification.
     */
    public function toWebPush($notifiable)
    {
        return (new WebPushMessage())
            ->title("Weather alert for location: {$this->location}")
            ->body("Current temperatures are {$this->alert_type} {$this->threshold} degrees Celsius, with {$this->temperature} degrees now recorded.");
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'temperature' => $this->temperature,
            'location' => $this->location,
            'alert_type' => $this->alert_type,
            'threshold' => $this->threshold,
        ];
    }
}
