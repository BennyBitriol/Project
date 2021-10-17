<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LineMeController extends Controller
{
    public function __construct()
     {
         $this->middleware(['auth:api']);
     }

    public function __invoke(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'line_id' => $user->id,
            'line_avatar' => $user->avatar,
            'line_name' => $user->name,
        ]);
    }
}
