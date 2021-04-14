<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    /**
     * @OA\Get(
     *  path="/password/forgot",
     *  tags={"Authentication"},
     *  summary="Forgot Pasword",
     *  operationId="forgotPassword",
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
     *  @OA\Response(response=201,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="Not found"),
     *  @OA\Response(response=403,description="Forbidden")
     *)
     **/
    /**
     * Forgot password
     *
     * @param  \App\Http\Requests\Auth\ForgotPasswordRequest
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forgot(ForgotPasswordRequest $request)
    {
        $credentials = $request->validated();
        Password::sendResetLink($credentials);

        return response()->json([
            'success'   => true,
            'message'   => 'Reset password link sent on your email!'
        ], 200);
    }

    /**
     * Response token from email
     *
     * @param  \Illuminate\Http\Request;
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getToken(Request $request)
    {
        return response()->json([
            'success'   => true,
            'data'      => [
                'email'     => $request->email,
                'token'     => $request->token,
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *  path="/password/reset",
     *  tags={"Authentication"},
     *  summary="Reset Pasword",
     *  operationId="resetPassword",
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
     *      name="token",
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
     *      name="password_confirmation",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *  ),
     * 
     *  @OA\Response(response=201,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="Not found"),
     *  @OA\Response(response=403,description="Forbidden")
     *)
     **/
    /**
     * Forgot password
     *
     * @param  \App\Http\Requests\Auth\ResetPasswordRequest
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reset(ResetPasswordRequest $request)
    {
        $resetPasswordStatus = Password::reset($request->validated(), function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
        });

        if ($resetPasswordStatus == Password::INVALID_TOKEN) {
            return response()->json([
                'success'   => true,
                'message'   => 'Token is invalid!'
            ], 401);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Password has been successfully changed!'
        ], 200);
    }

    /**
     * @OA\Patch(
     *  path="/password/change",
     *  tags={"Authentication"},
     *  summary="Change Pasword",
     *  operationId="changePassword",
     *  security={
     *         {"bearerAuth": {}}
     *      },
     *  @OA\Parameter(
     *      name="old_password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string",
     *           format="password"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="new_password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string",
     *           format="password"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="password_confirmation",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string",
     *           format="password"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="_method",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          default="PATCH"
     *      )
     *  ),
     * 
     *  @OA\Response(response=201,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="Not found"),
     *  @OA\Response(response=403,description="Forbidden")
     *)
     **/
    /**
     * Change password
     *
     * @param  \App\Http\Requests\Auth\ChangePasswordRequest
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function change(ChangePasswordRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success'   => false,
                'message'   => 'Old password is incorrect!'
            ]);
        }
        User::where('id', $user->id)->update(['password' => bcrypt($request->new_password)]);

        return response()->json([
            'success'   => true,
            'message'   => 'Password has been successfully changed!'
        ]);
    }
}
