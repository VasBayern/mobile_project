<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
     *  summary="Verify Email",
     *  operationId="verifyEmail",
     *  description="Verify account by link received from email",
     *  security={{"bearerAuth": {}}},
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
     *  @OA\Response(response=201,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="Not found"),
     *  @OA\Response(response=403,description="Forbidden")
     *)
     **/

    /**
     * Verify email
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function verify($id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Signature is invalid!'
            ], 401);
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Email verify success!'
        ], 200);
    }

    /**
     * @OA\Get(
     *  path="/email/resend",
     *  tags={"Authentication"},
     *  summary="Resend Email",
     *  operationId="resendEmail",
     *  description="Resend email to verify",
     *  security={{"bearerAuth": {}}},
     *
     *  @OA\Response(response=201,description="Success",@OA\MediaType( mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="Not found"),
     *  @OA\Response(response=403,description="Forbidden")
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
                'message'   => 'Account has verified!'
            ], 200);
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->json([
            'success'   => true,
            'message'   => 'Email sent!'
        ], 200);
    }
}
