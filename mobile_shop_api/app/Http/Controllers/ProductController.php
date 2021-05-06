<?php

namespace App\Http\Controllers;

use App\Exports\ProductMultiSheetExport;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\ImageProduct;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use App\Traits\HandleImageTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ProductController extends Controller
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
     *  path="/products",
     *  tags={"Product"},
     *  summary="Get All Product",
     *  operationId="getProducts",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Parameter(name="sort", in="query", required=true, description="sort by column: 0-id, 1-name, 2-price_core, 3-price, 4-sort_no, 5-home, 6-new, 7-created_at", @OA\Schema(type="integer", default="0", enum={0,1,2,3,4,5,6,7})),
     *  @OA\Parameter(name="order", in="query", required=true, description="sort by order: 0-ASC, 1-DESC", @OA\Schema(type="integer", default="0", enum={0,1})),
     *  @OA\Parameter(name="per_page", in="query", required=true, description="sort by paginate page: 0-10, 1-25, 2-50, 3-100", @OA\Schema(type="integer", default="0", enum={0,1,2,3})),
     *  @OA\Parameter(name="start_date", in="query", required=false, description="start date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="end_date", in="query", required=false, description="end date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="search", in="query", required=false, description="search by name", @OA\Schema(type="string")),
     *  @OA\Response(response=200, description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object", ref="#/components/schemas/Product"),
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
            $products = (new Product())->getProductWithOrder($condition);

            return $this->respondWithResourceCollection(ProductResource::collection($products));
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *  path="/products",
     *  tags={"Product"},
     *  summary="Add Product With Form Data",
     *  operationId="storeProduct",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"name", "category_id", "brand_id", "price_core", "price", "sort_no", "home", "new", "images", "introduction", "additional_incentives", "description", "specification"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="brand_id", type="integer"),
     *              @OA\Property(property="price_core", type="number", multipleOf=1000),
     *              @OA\Property(property="price", type="number", multipleOf=1000),
     *              @OA\Property(property="sort_no", type="integer", default=0),
     *              @OA\Property(property="home", type="string", default="0", enum={"0", "1"}, description="Show in homepage => 0: False, 1: True",),
     *              @OA\Property(property="new", type="string", default="0", enum={"0", "1"}, description="New product => 0: False, 1: True",),
     *              @OA\Property(property="introduction", type="string", default="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
     *              @OA\Property(property="additional_incentives", type="string", default="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
     *              @OA\Property(property="description", type="string", default="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
     *              @OA\Property(property="specification", type="string", default="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
     *              @OA\Property(property="images[]", type="array", items={"type": "string", "format"="binary"}),
     *          )
     *      )
     *  ),
     *  @OA\Response(response=201, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Product"),
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
     * @param  \App\Http\Requests\Product\StoreProductRequest $request
     * 
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $product = Product::create($request->all());

            if ($request->hasFile('images')) {
                $directory = Product::DIRECTORY_PATH . $product->id;
                foreach ($request->file('images') as $key => $file) {
                    $imageName = $key == 0 ? $request->name : $request->name . '-' . $key;
                    $path = $this->handleUploadImage($directory, $imageName, $file);

                    ImageProduct::create([
                        'product_id' => $product->id,
                        'path' => $path
                    ]);
                }
            }
            DB::commit();

            return $this->respondWithResource(new ProductDetailResource($product), 'Thêm thành công', 201);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Get(
     *  path="/products/{id}",
     *  tags={"Product"},
     *  summary="Find Product By ID",
     *  operationId="showProduct",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *  @OA\Response(response=200, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Product"),
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
            $product = Product::findOrFail($id);
            return $this->respondWithResource(new ProductDetailResource($product));
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return JsonResponse
     */
    public function edit(Request $request, $id)
    {
        //
    }

    /**
     * @OA\Post(
     *  path="/products/{id}",
     *  tags={"Product"},
     *  summary="Update Product With Form Data",
     *  operationId="updateProduct",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer",)),
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"name", "category_id", "brand_id", "price_core", "price", "sort_no", "home", "new", "introduction", "additional_incentives", "description", "specification", "_method"},
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="brand_id", type="integer"),
     *              @OA\Property(property="price_core", type="number", multipleOf=1000),
     *              @OA\Property(property="price", type="number", multipleOf=1000),
     *              @OA\Property(property="sort_no", type="integer", default=0),
     *              @OA\Property(property="home", type="string", default="0", enum={"0", "1"}, description="Show in homepage => 0: False, 1: True",),
     *              @OA\Property(property="new", type="string", default="0", enum={"0", "1"}, description="New product => 0: False, 1: True",),
     *              @OA\Property(property="introduction", type="string", default="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
     *              @OA\Property(property="additional_incentives", type="string", default="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
     *              @OA\Property(property="description", type="string", default="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
     *              @OA\Property(property="specification", type="string", default="Lorem Ipsum is simply dummy text of the printing and typesetting industry"),
     *              @OA\Property(property="_method", type="string", default="PUT"),
     *              
     *              @OA\Property(property="delete_images", type="array", items={"type": "string"}),
     *          )
     *      )
     *  ),
     *  @OA\Response(response=201, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Product"),
     *      )
     *  ),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=403,description="Forbidden"),
     *  @OA\Response(response=422,description="Unprocessable entity"),
     *)
     **/
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Product\UpdateProductRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $product->update($request->all());

            DB::commit();

            return $this->respondWithResource(new ProductDetailResource($product), 'Sửa thành công');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Delete(
     *  path="/products/{id}",
     *  tags={"Product"},
     *  summary="Delete Product By ID",
     *  operationId="destroyProduct",
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
     * @param int $id
     * 
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $directory = Product::DIRECTORY_PATH . $id;
            $this->removeImageDirectory($directory);

            $product->delete();

            return $this->respondSuccess('Xoá thành công');
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * @OA\Get(
     *  path="/products/excel/export",
     *  tags={"Product"},
     *  summary="Export Excel Product",
     *  operationId="exportProduct",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Parameter(name="sort", in="query", required=true, description="sort by column: 0-id, 1-name, 2-price_core, 3-price, 4-sort_no, 5-home, 6-new, 7-created_at", @OA\Schema(type="integer", default="0", enum={0,1,2,3,4,5,6,7})),
     *  @OA\Parameter(name="order", in="query", required=true, description="sort by order: 0-ASC, 1-DESC", @OA\Schema(type="integer", default="0", enum={0,1})),
     *  @OA\Parameter(name="per_page", in="query", required=true, description="sort by paginate page: 0-10, 1-25, 2-50, 3-100", @OA\Schema(type="integer", default="0", enum={0,1,2,3})),
     *  @OA\Parameter(name="start_date", in="query", required=false, description="start date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="end_date", in="query", required=false, description="end date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="search", in="query", required=false, description="search by name", @OA\Schema(type="string")),
     *  @OA\Response(response=200, description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object", ref="#/components/schemas/Product"),
     *     )
     *  ),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *)
     */
    /**
     * Export excel product
     * 
     * @param  \Illuminate\Http\Request $request
     * 
     * @return excel
     */
    public function export(Request $request)
    {
        try {
            $condition = $request->all();
            $fileName = 'san-pham-' . now()->format('d-m-Y-his') . '.xlsx';

            return $this->excel->download(new ProductMultiSheetExport($condition), $fileName);
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
}
