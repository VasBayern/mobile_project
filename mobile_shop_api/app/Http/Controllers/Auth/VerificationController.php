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
