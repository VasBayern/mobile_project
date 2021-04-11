<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *  path="/user",
     *  tags={"Authentication"},
     *  summary="Get User",
     *  operationId="getUser",
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
