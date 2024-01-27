<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;
    public function login(LoginUserRequest $request) {
        $validated = $request->validated();
        if(!Auth::attempt($validated)){
            return $this->error('', 'Credentials do not match', 401);
        }
        $user = User::where('email', $request->email)->first();//find by email
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api token of ' . $user->name)->plainTextToken, // grant token
        ]);
    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
            'message' => ' you have successfully been logged out and your token hasd been deleted',
        ]);
    }

    public function register(StoreUserRequest $request) {

        $request->validated($request->all());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]); // laravel data
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken
        ]);
    }
}
