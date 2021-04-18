<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryDetailResource;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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
     *  @OA\Parameter(
     *      name="sort", 
     *      in="query", 
     *      required=true, 
     *      description="sort by column: 0-id, 1-name, 2-sort_no, 3-home, 4-image", 
     *      @OA\Schema(type="integer", default="0", enum={0,1,2,3,4})
     *  ),
     *  @OA\Parameter(
     *      name="order", 
     *      in="query", 
     *      required=true, 
     *      description="sort by order: 0-ASC, 1-DESC", 
     *      @OA\Schema(type="integer", default="0", enum={0,1})
     *  ),
     *  @OA\Parameter(
     *      name="per_page", 
     *      in="query", 
     *      required=true, 
     *      description="sort by paginate page: 10, 25, 50, 100", 
     *      @OA\Schema(type="integer", default="10", enum={10,25,50,100})
     *  ),
     *  @OA\Parameter(
     *      name="search", 
     *      in="query", 
     *      required=false,
     *      description="search by name", 
     *      @OA\Schema(type="string")
     *  ),
     *  @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object", ref="#/components/schemas/Category"),
     *     )
     *  ),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *)
     **/
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $page = $request->all();

            $search = isset($page['search']) ? $page['search'] : '';
            $perPage = $page['per_page'];
            $sortOrder = ($page['order'] == 1) ? 'DESC' : 'ASC';
            $sortColumn = '';
            switch ($page['sort']) {
                case 0:
                    $sortColumn = 'id';
                    break;
                case 1:
                    $sortColumn = 'name';
                    break;
                case 2:
                    $sortColumn = 'sort_no';
                    break;
                case 3:
                    $sortColumn = 'home';
                    break;
                case 4:
                    $sortColumn = 'image';
                    break;
                default:
                    throw new Exception("Không tìm thấy trường này");
                    break;
            }

            $categories = Category::when($search, function ($query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })->orderBy($sortColumn, $sortOrder)->paginate($perPage);

            return CategoryDetailResource::collection($categories)->response()->getData(true);
        } catch (Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 422);
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
     *  @OA\Response(
     *      response=201,
     *      description="Success",
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
        ], 201);
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
     *      @OA\Schema(type="integer")
     *  ),
     * 
     *  @OA\Response(
     *      response=200,
     *      description="Success",
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
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'success'   => true,
                'data'      => new CategoryDetailResource($category)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 404);
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
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="sort_no", type="integer", default="0"),
     *              @OA\Property(property="home", type="integer", enum={0, 1}, description="Show in homepage => 0: False, 1: True"),
     *              @OA\Property(property="image", type="file"),
     *              @OA\Property(property="_method", type="string", default="PUT"),
     *          )
     *      )
     *  ),
     *  @OA\Response(
     *      response=201,
     *      description="Success",
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
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 404);
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
     *      @OA\Schema(type="integer")
     *  ),
     * 
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
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 404);
        }
    }
}
