<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Register user
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return response
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $token =  $user->createToken('browser')->plainTextToken;
        $user->sendEmailVerificationNotification();

        return response()->json([
            'success'       => true,
            'access_token'  => $token,
            'token_type'    => 'Bearer',
        ], 200);
    }
}
