<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\sensor_data;
use Illuminate\Http\Request;
use App\User;


class SensorData extends Controller
{
    public function sensordata() {
        return sensor_data::all();
    }

    public function sensordata1(){
        $value = sensor_data::where('sensor_id', "=" , 1)->limit(20)->pluck('value');
        $date = sensor_data::where('sensor_id', "=" , 1)->limit(20)->pluck('date');
        return $value;
    }
}