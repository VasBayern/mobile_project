<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * 
     * @return \Symfony\Component\HttpFoundation\Response
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

    /**
     * @OA\Put(
     *  path="/user/update",
     *  tags={"Authentication"},
     *  summary="Update User",
     *  operationId="updateUser",
     *  security={
     *         {"bearerAuth": {}}
     *      },
     *  @OA\Parameter(
     *      name="name",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="phone",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="birthday",
     *      in="query",
     *      required=false,
     *      description="format: dd/mm/yyyy",
     *      @OA\Schema(
     *          type="string",
     *          format="date"
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="sex",
     *      in="query",
     *      required=false,
     *      description="0: Male, 1: Femail, 2: Orther",
     *      @OA\Schema(
     *           type="integer",
     *           minimum="0",
     *           maximum="2",
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="address",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string",
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="image",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string",
     *           format="binary"
     *      )
     *  ),
     *  @OA\RequestBody(
     *     @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="avatar",
     *                  type="file",
     *                  description="Choose a image"
     *              ),
     *          )
     *      )
     *  ),
     *  @OA\Parameter(
     *      name="_method",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *          type="string",
     *          default="PUT"
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
     * Update information
     *
     * @param App\Http\Requests\Auth\UpdateUserRequest;
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(UpdateUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('id', Auth::user()->id)->first();
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('sex')) {
            $user->sex = $request->sex;
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        if ($request->has('birthday')) {
            $user->birthday = DateTime::createFromFormat('d/m/Y', $request->birthday)->format('Y-m-d H:i:s');
        }
        if ($request->hasFile('avatar')) {
            $imageName = $request->file('avatar')->getClientOriginalName();
            //$imageName = Str::slug($user->name) . '.' . $request->avatar->extension();
            $path = Storage::putFileAs(
                'public/hinh-anh/tai-khoan/' . $user->id,
                $request->file('avatar'),
                $imageName
            );
            $user->avatar = Storage::url($path);
        }
        $user->save();

        return response()->json([
            'success'   => true,
            'message'   => 'Information account has been updated',
            'data'      => $user
        ]);
    }
}
