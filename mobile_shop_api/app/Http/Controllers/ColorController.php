<?php

namespace App\Http\Controllers;

use App\Exports\ColorExport;
use App\Http\Requests\Color\StoreColorRequest;
use App\Http\Requests\Color\UpdateColorRequest;
use App\Http\Resources\ColorResource;
use App\Models\Color;
use App\Traits\ApiResponseTrait;
use App\Traits\HandleImageTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ColorController extends Controller
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
     *  path="/colors",
     *  tags={"Color"},
     *  summary="Get All Color",
     *  operationId="getColors",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Parameter(name="sort", in="query", required=true, description="sort by column: 0-id, 1-name, 2-code, 3-created_at", @OA\Schema(type="integer", default="0", enum={0,1,2,3})),
     *  @OA\Parameter(name="order", in="query", required=true, description="sort by order: 0-ASC, 1-DESC", @OA\Schema(type="integer", default="0", enum={0,1})),
     *  @OA\Parameter(name="per_page", in="query", required=true, description="sort by paginate page: 0-10, 1-25, 2-50, 3-100", @OA\Schema(type="integer", default="0", enum={0,1,2,3})),
     *  @OA\Parameter(name="start_date", in="query", required=false, description="start date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="end_date", in="query", required=false, description="end date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="search", in="query", required=false, description="search by name", @OA\Schema(type="string")),
     *  @OA\Response(response=200, description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object", ref="#/components/schemas/Color"),
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
            $colors = (new Color())->getColorWithOrder($condition);

            return $this->respondWithResourceCollection(ColorResource::collection($colors));
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Post(
     *  path="/colors",
     *  tags={"Color"},
     *  summary="Add Color With Form Data",
     *  operationId="storeColor",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"name", "code"},
     *          @OA\Property(property="name", type="string", example="Đỏ"),
     *          @OA\Property(property="code", type="string", example="#ff0000"),
     *      ),
     *  ),
     *  @OA\Response(response=201, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Color"),
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
     * @param  \App\Http\Requests\Color\StoreColorRequest $request
     * 
     * @return JsonResponse
     */
    public function store(StoreColorRequest $request)
    {
        $color = Color::create($request->all());

        return $this->respondWithResource(new ColorResource($color), 'Thêm thành công', 201);
    }

    /**
     * @OA\Get(
     *  path="/colors/{id}",
     *  tags={"Color"},
     *  summary="Find Color By ID",
     *  operationId="showColor",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *  @OA\Response(response=200, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Color"),
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
            $color = Color::findOrFail($id);
            return $this->respondWithResource(new ColorResource($color));
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * @OA\Put(
     *  path="/colors/{id}",
     *  tags={"Color"},
     *  summary="Update Color With Form Data",
     *  operationId="updateColor",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer",)),
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"name", "code", "_method"},
     *          @OA\Property(property="name", type="string", example="Đỏ"),
     *          @OA\Property(property="code", type="string", example="#ff0000"),
     *      ),
     *  ),
     *  @OA\Response(response=200, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Color"),
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
     * @param  \App\Http\Requests\Color\UpdateColorRequest $request
     * @param  int  $id
     * 
     * @return JsonResponse
     */
    public function update(UpdateColorRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $color = Color::findOrFail($id);
            $color->update($request->all());
            DB::commit();

            return $this->respondWithResource(new ColorResource($color), 'Sửa thành công');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Delete(
     *  path="/colors/{id}",
     *  tags={"Color"},
     *  summary="Delete Color By ID",
     *  operationId="destroyColor",
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
            $color = Color::findOrFail($id);
            $color->delete();

            return $this->respondSuccess('Xoá thành công');
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * @OA\Get(
     *  path="/colors/excel/export",
     *  tags={"Color"},
     *  summary="Export Excel Color",
     *  operationId="exportColor",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Parameter(name="sort", in="query", required=true, description="sort by column: 0-id, 1-name, 2-code, 3-created_at", @OA\Schema(type="integer", default="0", enum={0,1,2,3})),
     *  @OA\Parameter(name="order", in="query", required=true, description="sort by order: 0-ASC, 1-DESC", @OA\Schema(type="integer", default="0", enum={0,1})),
     *  @OA\Parameter(name="per_page", in="query", required=true, description="sort by paginate page: 0-10, 1-25, 2-50, 3-100", @OA\Schema(type="integer", default="0", enum={0,1,2,3})),
     *  @OA\Parameter(name="start_date", in="query", required=false, description="start date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="end_date", in="query", required=false, description="end date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="search", in="query", required=false, description="search by name", @OA\Schema(type="string")),
     *  @OA\Response(response=200, description="Success"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *)
     **/
    public function export(Request $request)
    {
        try {
            $condition = $request->all();
            $fileName = 'mau-sac-' . now()->format('d-m-Y-his') . '.xlsx';

            return $this->excel->download(new ColorExport($condition), $fileName);
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
}
