<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Models\User;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *  path="/user",
     *  tags={"Authentication"},
     *  summary="Get Current User Information",
     *  operationId="getUser",
     *  security={
     *      {"bearerAuth": {}}
     *  },
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

        return response()->json([
            'status'    => true,
            'data'      => $user,
        ], 200);
    }

    /**
     * @OA\Post(
     *  path="/user/update",
     *  tags={"Authentication"},
     *  summary="Update Current User Information",
     *  operationId="updateUser",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="phone",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="birthday",
     *                  type="string",
     *                  format="date",
     *              ),
     *              @OA\Property(
     *                  property="sex",
     *                  type="integer",
     *                  default="0",
     *                  minimum="0",
     *                  maximum="2",
     *                  enum={0, 1, 2},
     *                  description="0: Male, 1: Female, 2: Orther",
     *              ),
     *              @OA\Property(
     *                  property="address",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="avatar",
     *                  type="file",
     *              ),
     *              @OA\Property(
     *                  property="_method",
     *                  type="string",
     *                  default="PUT",
     *              ),
     *          )
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
        DB::beginTransaction();
        try {
            $user = User::where('id', Auth::user()->id)->first();
            $directory = 'public/hinh-anh/tai-khoan/' . $user->id;

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
            if ($request->has('name')) {
                $name = $user->name;
                $requestName = $request->name;
                $user->name = $requestName;

                if (!$request->hasFile('avatar') && Storage::exists($directory)) {
                    $oldPathImage = Storage::files($directory)[0];
                    $oldPathImageArr = explode("/", $oldPathImage);
                    $oldNameImage = end($oldPathImageArr);

                    $newNameImage = Str::replaceFirst(Str::slug($name), Str::slug($requestName), $oldNameImage);
                    $newPathImage = $directory . '/' . $newNameImage;

                    if ($newPathImage != $oldPathImage) {
                        Storage::move($oldPathImage, $newPathImage);
                        $user->avatar = Storage::url($newPathImage);
                    }
                }
            }
            if ($request->hasFile('avatar')) {
                if (Storage::exists($directory)) {
                    Storage::deleteDirectory($directory);
                }
                $userName = $request->has('name') ? $request->name : $user->name;
                $imageName = Str::slug($userName) . '.' . $request->avatar->extension();
                $imagePath = Storage::putFileAs(
                    $directory,
                    $request->file('avatar'),
                    $imageName
                );
                $user->avatar = Storage::url($imagePath);
            }
            $user->save();
            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Cập nhật thành công!',
                'data'      => $user
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Delete(
     *  path="/user/destroy/{id}",
     *  tags={"Authentication"},
     *  summary="Delete User Account By ID",
     *  description="Permission: Admin",
     *  operationId="destroyUser",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
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
     * Delete user
     *
     * @param  Illuminate\Http\Request $request
     * @param  integer $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::where(['id' => $id, 'role' => 3])->first();
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success'   => true,
                'message'   => 'Xóa thành công'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ]);
        }
    }
}
