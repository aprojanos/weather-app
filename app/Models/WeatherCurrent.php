<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherCurrent extends Model
{
    use HasFactory;

    protected $table = 'weather_current';
}
