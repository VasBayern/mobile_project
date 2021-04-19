<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *  path="/register",
     *  tags={"Authentication"},
     *  summary="Register New Account",
     *  operationId="register",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\RequestBody(
     *      required=true,
     *      description="Register Form",
     *      @OA\JsonContent(
     *          required={"email", "name", "password", "password_confirmation"},
     *          @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *          @OA\Property(property="name", type="string", example="Nguyen Van A"),
     *          @OA\Property(property="password", type="string", format="password", example="12345678"),
     *          @OA\Property(property="password_confirmation", type="string", format="password", example="12345678"),
     *      ),
     *  ),
     *  @OA\Response(response=201,description="Success", @OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=422,description="Unprocessable entity"),
     *)
     **/

    /**
     * Register user
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(RegisterRequest $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $token =  $user->createToken('browser')->plainTextToken;
        event(new Registered($user));

        return response()->json([
            'success'       => true,
            'message'       => 'Đăng kí thành công',
            'token_type'    => 'Bearer',
            'access_token'  => $token,
        ], 201);
    }
}
