<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get user information
     *
     * @param  Illuminate\Http\Request $request
     * @return response
     */
    public function getUser(Request $request)
    {
        $user = $request->user();

        if ($user) {
            return response()->json([
                'status'    => true,
                'data'      => $user,
            ], 200);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => "An error occurred!",
            ], 500);
        }
    }
}
