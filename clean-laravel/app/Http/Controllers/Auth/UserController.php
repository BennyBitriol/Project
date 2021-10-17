<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\users;
use Illuminate\Http\Request;
use App\User;


class UserController extends Controller
{
    public function User()
    {
        return users::where('line_avatar', "!=" , null)->get();
    }
}