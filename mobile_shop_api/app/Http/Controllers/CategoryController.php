<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryDetailResource;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponseTrait;
    /**
     * @OA\Get(
     *  path="/categories",
     *  tags={"Category"},
     *  summary="Get All Category",
     *  operationId="getCategories",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Parameter(name="sort", in="query", required=true, description="sort by column: 0-id, 1-name, 2-sort_no, 3-home, 4-image, 5-created_at", @OA\Schema(type="integer", default="0", enum={0,1,2,3,4,5})),
     *  @OA\Parameter(name="order", in="query", required=true, description="sort by order: 0-ASC, 1-DESC", @OA\Schema(type="integer", default="0", enum={0,1})),
     *  @OA\Parameter(name="per_page", in="query", required=true, description="sort by paginate page: 0-10, 1-25, 2-50, 3-100", @OA\Schema(type="integer", default="0", enum={0,1,2,3})),
     *  @OA\Parameter(name="start_date", in="query", required=false, description="start date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="end_date", in="query", required=false, description="end date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="search", in="query", required=false, description="search by name", @OA\Schema(type="string")),
     *  @OA\Response(response=200, description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object", ref="#/components/schemas/Category"),
     *     )
     *  ),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *)
     **/
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $page = $request->all();
            $categories = Category::getCategoryWithOrder($page);

            return $this->respondWithResourceCollection(CategoryDetailResource::collection($categories));
        } catch (Exception $exception) {
            return $this->respondError('Điều kiện không chính xác', 400, $exception);
        }
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
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="sort_no", type="integer", default="0"),
     *              @OA\Property(property="home", type="integer", default="0", enum={0, 1}, description="Show in homepage => 0: False, 1: True",),
     *              @OA\Property(property="image", type="file",),
     *          )
     *      )
     *  ),
     *  @OA\Response(response=201, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Category"),
     *      )
     *  ),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=403,description="Forbidden"),
     *  @OA\Response(response=422,description="Unprocessable entity"),
     *)
     **/
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Category\StoreCategoryRequest $request
     * 
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->all());
        $category->image = Category::handleUploadImage($category->id, $request->name, $request->image);
        $category->save();
        return $this->respondWithResource(new CategoryDetailResource($category), 'Thêm thành công', 201);
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
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *  @OA\Response(response=200, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Category"),
     *      )
     *  ),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=404,description="Not Found"),
     *)
     **/
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * 
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return $this->respondWithResource(new CategoryDetailResource($category));
        } catch (Exception $exception) {
            return $this->respondError('ID không tồn tại!', 404, $exception);
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
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer",)),
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"name", "home", "sort_no", "_method"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="sort_no", type="integer", default="0"),
     *              @OA\Property(property="home", type="integer", enum={0, 1}, description="Show in homepage => 0: False, 1: True"),
     *              @OA\Property(property="image", type="file"),
     *              @OA\Property(property="_method", type="string", default="PUT"),
     *          )
     *      )
     *  ),
     *  @OA\Response(response=200, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Category"),
     *      )
     *  ),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=403,description="Forbidden"),
     *  @OA\Response(response=404,description="Not found"),
     *  @OA\Response(response=405,description="Method not allow"),
     *  @OA\Response(response=422,description="Unprocessable entity"),
     *)
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Category\UpdateCategoryRequest $request
     * @param  int  $id
     * 
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::findOrFail($id);
            $categoryId = $category->id;
            $categoryImage = $category->image;
            $categoryName = $category->slug;

            $category->update($request->all());

            if ($request->hasFile('image')) {
                $category->image = Category::handleUploadImage($categoryId, $request->name, $request->image);
            } else {
                $category->image = Category::renameStorageImage($categoryId, $categoryName, $categoryImage, $request->name);
            }
            $category->save();
            DB::commit();

            return $this->respondWithResource(new CategoryDetailResource($category), 'Sửa thành công', 200);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->respondError('Có lỗi xảy ra. Vui lòng thử lại!', 400, $exception);
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
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *  @OA\Response(response=200,description="Success"),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=403,description="Forbidden"),
     *  @OA\Response(response=404,description="Not found"),
     *)
     **/
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * 
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            Category::removeImageDirectory($id);
            $category->delete();

            return $this->respondSuccess('Xoá thành công');
        } catch (Exception $exception) {
            return $this->respondError('ID không tồn tại!', 404, $exception);
        }
    }
}
