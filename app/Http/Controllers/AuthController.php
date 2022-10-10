<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function getToken()
    {
        $user = User::firstOrFail();

        $token = $user->createToken('myapp')->plainTextToken;
        
        return response([
            'token' => $token,
        ]);
    }
}
