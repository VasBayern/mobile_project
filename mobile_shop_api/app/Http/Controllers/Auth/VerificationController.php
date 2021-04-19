<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['verify']);
    }

    /**
     * @OA\Get(
     *  path="/email/verify/{id}",
     *  tags={"Authentication"},
     *  summary="Verify Email After Register",
     *  operationId="verifyEmail",
     *  description="Verify account by link had been received from email",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="id account",
     *      @OA\Schema(
     *           type="integer"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="expires",
     *      in="query",
     *      required=true,
     *      description="expires time of link",
     *      @OA\Schema(
     *           type="integer"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="hash",
     *      in="query",
     *      required=true,
     *      description="token hash",
     *      @OA\Schema(
     *           type="string"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="signature",
     *      in="query",
     *      description="signature",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *  ),
     *  @OA\Response(response=200,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=422,description="Unprocessable entity"),
     *)
     **/

    /**
     * Verify email
     *
     * @param integer $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function verify($id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Xác thực không thành công!'
            ], 422);
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Xác thực thành công!'
        ], 200);
    }

    /**
     * @OA\Get(
     *  path="/email/resend",
     *  tags={"Authentication"},
     *  summary="Resend Email To Verify",
     *  operationId="resendEmail",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *
     *  @OA\Response(response=200,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=422,description="Unprocessable entity"),
     *)
     **/

    /**
     * Resend email verification link
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resend()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Tài khoản đã xác thực từ trước!'
            ], 422);
        }
        event(new Registered(auth()->user()));

        return response()->json([
            'success'   => true,
            'message'   => 'Đã gửi email xác thực!'
        ], 200);
    }
}
