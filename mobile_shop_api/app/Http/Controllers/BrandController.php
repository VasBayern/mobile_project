<?php

namespace App\Http\Controllers;

use App\Exports\BrandMultiSheetExport;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Resources\BrandDetailResource;
use App\Models\Brand;
use App\Traits\ApiResponseTrait;
use App\Traits\HandleImageTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Excel;

class BrandController extends Controller
{
    use ApiResponseTrait, HandleImageTrait;

    private $excel;

    /**
     * Instantiate a new controller instance
     *
     * @param  \Maatwebsite\Excel\Excel  $excel
     * @return void
     */
    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    /**
     * @OA\Get(
     *  path="/brands",
     *  tags={"Brand"},
     *  summary="Get All Brand",
     *  operationId="getBrands",
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
     *        @OA\Property(property="data", type="object", ref="#/components/schemas/Brand"),
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
            $condition = $request->all();
            $brands = (new Brand)->getBrandWithOrder($condition);

            return $this->respondWithResourceCollection(BrandDetailResource::collection($brands));
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Post(
     *  path="/brands",
     *  tags={"Brand"},
     *  summary="Add Brand With Form Data",
     *  operationId="storeBrand",
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
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Brand"),
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
     * @param  \App\Http\Requests\brand\StoreBrandRequest $request
     * 
     * @return JsonResponse
     */
    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::create($request->all());
        $directory = Brand::DIRECTORY_PATH . $brand->id;
        $brand->image = $this->handleUploadImage($directory, $request->name, $request->image);
        $brand->save();

        return $this->respondWithResource(new BrandDetailResource($brand), 'Thêm thành công', 201);
    }

    /**
     * @OA\Get(
     *  path="/brands/{id}",
     *  tags={"Brand"},
     *  summary="Find Brand By ID",
     *  operationId="showBrand",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *  @OA\Response(response=200, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Brand"),
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
            $brand = Brand::findOrFail($id);
            return $this->respondWithResource(new BrandDetailResource($brand));
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * @OA\Post(
     *  path="/brands/{id}",
     *  tags={"Brand"},
     *  summary="Update Brand With Form Data",
     *  operationId="updateBrand",
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
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Brand"),
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
     * @param  \App\Http\Requests\Brand\UpdateBrandRequest $request
     * @param  int  $id
     * 
     * @return JsonResponse
     */
    public function update(UpdateBrandRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $brand = Brand::findOrFail($id);
            $directory = Brand::DIRECTORY_PATH . $id;
            $brandImage = $brand->image;
            $brandName = $brand->name;

            $brand->update($request->all());

            if ($request->hasFile('image')) {
                $brand->image = $this->handleUploadImage($directory, $request->name, $request->image);
            } else {
                $brand->image = $this->renameStorageImage($directory, $brandName, $brandImage, $request->name);
            }
            $brand->save();
            DB::commit();

            return $this->respondWithResource(new BrandDetailResource($brand), 'Sửa thành công');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Delete(
     *  path="/brands/{id}",
     *  tags={"Brand"},
     *  summary="Delete Brand By ID",
     *  operationId="destroyBrand",
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
            $brand = Brand::findOrFail($id);
            $directory = Brand::DIRECTORY_PATH . $id;
            $this->removeImageDirectory($directory);

            $brand->delete();

            return $this->respondSuccess('Xoá thành công');
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * @OA\Get(
     *  path="/brands/excel/export",
     *  tags={"Brand"},
     *  summary="Export Excel Brand",
     *  operationId="exportBrand",
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

     *  ),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *)
     **/
    public function export(Request $request)
    {
        try {
            $condition = $request->all();
            $fileName = 'danh-muc-' . now()->format('dmY-his') . '.xlsx';

            return $this->excel->download(new BrandMultiSheetExport($condition), $fileName);
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
}
