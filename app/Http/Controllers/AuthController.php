<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\RegisteredUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        if (User::whereEmail($data['email'])->count() > 0) {
            return response(['message' => 'Email is already taken'], 400);
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $user->notify(new RegisteredUser());

        return response(['message' => 'User successfully registered'], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        if (!$token = auth()->attempt($data)) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        return response(['access_token' => $token], 201);
    }
}
