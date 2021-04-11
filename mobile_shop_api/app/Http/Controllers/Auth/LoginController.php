<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Login user
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return response
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không chính xác'],
            ]);
        }
        $token = Auth::user()->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'success'       => true,
            'token_type'    => 'Bearer',
            'access_token'  => $token,
        ], 200);
    }

    /**
     * Logout user
     *
     * @param  \Illuminate\Http\Request $request
     * @return response
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout success'
        ], 200);
    }
}
