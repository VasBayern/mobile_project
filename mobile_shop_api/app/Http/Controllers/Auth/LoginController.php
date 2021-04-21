<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use ApiResponseTrait;
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
            return $this->respondUnprocessableEntity(null, 'Email hoặc mật khẩu không chính xác!');
        }
        $token = Auth::user()->createToken($request->device_name)->plainTextToken;

        return $this->respondAuthenticated('Đăng nhập thành công', $token);
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

        return $this->respondSuccess('Đăng xuất thành công!');
    }
}
