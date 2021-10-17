<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class presentcontroller extends Controller
{
    public function Challenge()
    {
        Artisan::call('challenge:notification');
    }

    public function ChallengeDaily()
    {
        Artisan::call('challengedaily:notification');
    }

    public function ML2()
    {
        Artisan::call('ML2:notification');
    }
    
    public function MorningNotification()
    {
        Artisan::call('morning:notification');
    }

    public function UsageNotification()
    {
        Artisan::call('usage:notification');
    }
}
