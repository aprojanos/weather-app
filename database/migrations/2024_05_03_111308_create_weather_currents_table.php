<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weather_current', function (Blueprint $table) {
            $table->id();
            $table->string('location_name');
            $table->string('location_region');
            $table->string('location_country');
            $table->float('location_lat', 6);
            $table->float('location_lon', 6);
            $table->float('temp_c', 4);
            $table->float('feelslike_c', 4);
            $table->string('condition_text');
            $table->string('condition_icon');
            $table->float('wind_kph', 4);
            $table->smallInteger('wind_degree');
            $table->smallInteger('pressure_mb');
            $table->float('precip_mm', 4);
            $table->tinyInteger('humidity');
            $table->float('aqi_pm2_5', 4);
            $table->string('aqi_us_epa_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_current');
    }
};
