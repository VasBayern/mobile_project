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
     *         {"bearerAuth": {}}
     *      },
     *  @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="device_name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string",
     *           default="Browser"
     *      )
     *  ),
     *  @OA\Response(response=201,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="Not found"),
     *  @OA\Response(response=403,description="Forbidden")
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
     * @OA\Post(
     *  path="/logout",
     *  tags={"Authentication"},
     *  summary="Logout",
     *  operationId="logout",
     *  security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Response(response=201,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="Not found"),
     *  @OA\Response(response=403,description="Forbidden")
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
            'message' => 'Logout success'
        ], 200);
    }
}
