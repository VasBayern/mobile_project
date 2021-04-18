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
     * @OA\Post(
     *  path="/login",
     *  tags={"Authentication"},
     *  summary="Login",
     *  operationId="login",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\RequestBody(
     *      required=true,
     *      description="Register Form",
     *      @OA\JsonContent(
     *          required={"email", "password", "device_name"},
     *          @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *          @OA\Property(property="password", type="string", format="password", example="12345678"),
     *          @OA\Property(property="device_name", type="string", example="browser"),
     *      ),
     *  ),
     * 
     *  @OA\Response(response=200,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=422,description="Unprocessable entity"),
     *)
     **/

    /**
     * Login user
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không chính xác'],
            ]);
        }
        $token = Auth::user()->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'success'       => true,
            'message'       => 'Đăng nhập thành công',
            'token_type'    => 'Bearer',
            'access_token'  => $token,
        ], 200);
    }

    /**
     * @OA\Post(
     *  path="/logout",
     *  tags={"Authentication"},
     *  summary="Logout Current User",
     *  operationId="logout",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Response(response=200,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *)
     **/
    /**
     * Logout user
     *
     * @param  \Illuminate\Http\Request $request
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công!'
        ], 200);
    }
}
