<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryDetailResource;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**

 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *  path="/categories",
     *  tags={"Category"},
     *  summary="Get All Category",
     *  operationId="getCategories",
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::when(request('search'), function ($query) {
            $query->where('name', 'like', '%' . request('search') . '%');
        })->orderBy('sort_no', 'asc')->paginate(10);

        return CategoryDetailResource::collection($categories)->response()->getData(true);
    }

    /**
     * @OA\Post(
     *  path="/categories",
     *  tags={"Category"},
     *  summary="Add Category With Form Data",
     *  operationId="storeCategory",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"name", "home", "sort_no", "image"},
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="sort_no",
     *                  type="integer",
     *                  default="0",
     *              ),
     *              @OA\Property(
     *                  property="home",
     *                  type="integer",
     *                  default="0",
     *                  minimum="0",
     *                  maximum="1",
     *                  description="0: False, 1: True",
     *              ),
     *              @OA\Property(
     *                  property="image",
     *                  type="file",
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
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Category\StoreCategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->all());

        $imageName = Str::slug($request->name) . '.' . $request->image->extension();
        $imagePath = Storage::putFileAs(
            'public/hinh-anh/danh-muc/' . $category->id,
            $request->file('image'),
            $imageName
        );

        $category->image = Storage::url($imagePath);
        $category->save();

        return response()->json([
            'succees'   => true,
            'message'   => "Thêm thành công!",
            'data'      => new CategoryDetailResource($category)
        ]);
    }

    /**
     * @OA\Get(
     *  path="/categories/{id}",
     *  tags={"Category"},
     *  summary="Find Category By ID",
     *  operationId="showCategory",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="integer",
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'success'   => true,
                'data'      => new CategoryDetailResource($category)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Post(
     *  path="/categories/{id}",
     *  tags={"Category"},
     *  summary="Update Category With Form Data",
     *  operationId="updateCategory",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="integer",
     *      )
     *  ),
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"name", "home", "sort_no", "_method"},
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="sort_no",
     *                  type="integer",
     *                  default="0",
     *              ),
     *              @OA\Property(
     *                  property="home",
     *                  type="integer",
     *                  default="0",
     *                  minimum="0",
     *                  maximum="1",
     *                  description="0: False, 1: True",
     *              ),
     *              @OA\Property(
     *                  property="_method",
     *                  type="string",
     *                  default="PUT",
     *              ),
     *              @OA\Property(
     *                  property="image",
     *                  type="file",
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
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Category\UpdateCategoryRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::findOrFail($id);
            $directory = 'public/hinh-anh/danh-muc/' . $category->id;
            $oldImageName = $category->slug;
            $oldImageExtension = substr($category->image, -3);

            $category->update($request->all());

            if ($request->hasFile('image')) {
                Storage::deleteDirectory($directory);
                $newImageName = Str::slug($request->name) . '.' . $request->image->extension();
                $newPathImage = Storage::putFileAs(
                    $directory,
                    $request->file('image'),
                    $newImageName
                );

                $category->image = Storage::url($newPathImage);
                $category->save();
            } else {
                $oldPathImage = $directory . '/' . $oldImageName . '.' . $oldImageExtension;
                $newPathImage = $directory . '/' . $category->slug . '.' . $oldImageExtension;
                Storage::move($oldPathImage, $newPathImage);

                $category->image = Storage::url($newPathImage);
                $category->save();
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Sửa thành công',
                'data'      => new CategoryDetailResource($category)
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
     *  path="/categories/{id}",
     *  tags={"Category"},
     *  summary="Delete Category By ID",
     *  operationId="destroyCategory",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *          type="integer",
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $category = Category::findOrFail($id);
            $directory = 'public/hinh-anh/danh-muc/' . $category->id;
            if (Storage::exists($directory)) {
                Storage::deleteDirectory($directory);
            }
            $category->delete();

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
