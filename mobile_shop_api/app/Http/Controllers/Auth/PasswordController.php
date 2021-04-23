<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *  path="/password/forgot",
     *  tags={"Account"},
     *  summary="Enter email to reset password",
     *  description="Enter the registration email, then receive link reset password from email",
     *  operationId="forgotPassword",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *  ),
     *  @OA\Response(response=200,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=404,description="Not found"),
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

        return $this->respondSuccess('Mã đặt lại mật khẩu đã được gửi tới email của bạn!');
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
        return $this->respondSuccess(
            'Token reset password',
            [
                'email'     => $request->email,
                'token'     => $request->token,
            ],
        );
    }

    /**
     * @OA\Post(
     *  path="/password/reset",
     *  tags={"Account"},
     *  summary="Reset Pasword",
     *  description="Enter token from link had been sent to your email to reset password",
     *  operationId="resetPassword",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"email", "token", "password", "password_confirmation"},
     *          @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *          @OA\Property(property="token", type="string"),
     *          @OA\Property(property="password", type="string", format="password", example="12345678"),
     *          @OA\Property(property="password_confirmation", type="string", format="password", example="12345678"),
     *      ),
     *  ),
     *  @OA\Response(response=200,description="Success", @OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=422,description="Unprocessable entity"),
     *)
     **/
    /**
     * Reset password
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
            return $this->respondUnprocessableEntity(null, 'Mã Token không chính xác!');
        }
        return $this->respondSuccess('Thay đổi mật khẩu thành công!');
    }

    /**
     * @OA\Patch(
     *  path="/password/change",
     *  tags={"Account"},
     *  summary="Change Pasword",
     *  operationId="changePassword",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"email", "token", "password", "password_confirmation"},
     *          @OA\Property(property="old_password", type="string", format="password", example="12345678"),
     *          @OA\Property(property="new_password", type="string", format="password", example="123456789"),
     *          @OA\Property(property="password_confirmation", type="string", format="password", example="123456789"),
     *      ),
     *  ),
     * 
     *  @OA\Response(response=200,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=422,description="Unprocessable entity"),
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
        $user = Auth::user();
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->respondUnprocessableEntity(null, 'Mật khẩu hiện tại không đúng!');
        }
        User::where('id', $user->id)->update(['password' => bcrypt($request->new_password)]);

        return $this->respondSuccess('Thay đổi mật khẩu thành công!');
    }
}
