<?php

namespace App\Http\Controllers;

use App\Exports\RamExport;
use App\Http\Requests\Ram\StoreRamRequest;
use App\Http\Requests\Ram\UpdateRamRequest;
use App\Http\Resources\RamResource;
use App\Models\Ram;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class RamController extends Controller
{
    use ApiResponseTrait;

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
     *  path="/rams",
     *  tags={"Ram"},
     *  summary="Get All Ram",
     *  operationId="getRams",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Parameter(name="sort", in="query", required=true, description="sort by column: 0-id, 1-name, 2-created_at", @OA\Schema(type="integer", default="0", enum={0,1,2})),
     *  @OA\Parameter(name="order", in="query", required=true, description="sort by order: 0-ASC, 1-DESC", @OA\Schema(type="integer", default="0", enum={0,1})),
     *  @OA\Parameter(name="per_page", in="query", required=true, description="sort by paginate page: 0-10, 1-25, 2-50, 3-100", @OA\Schema(type="integer", default="0", enum={0,1,2,3})),
     *  @OA\Parameter(name="start_date", in="query", required=false, description="start date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="end_date", in="query", required=false, description="end date to filter (dd/mm/yyyy)", @OA\Schema(type="string", format="date")),
     *  @OA\Parameter(name="search", in="query", required=false, description="search by name", @OA\Schema(type="string")),
     *  @OA\Response(response=200, description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object", ref="#/components/schemas/Ram"),
     *     )
     *  ),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *)
     **/
    /**
     * Display a listing of the resource.
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $condition = $request->all();
            $rams = (new Ram())->getRamWithOrder($condition);

            return $this->respondWithResourceCollection(RamResource::collection($rams));
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Post(
     *  path="/rams",
     *  tags={"Ram"},
     *  summary="Add Ram",
     *  operationId="storeRam",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"name"},
     *          @OA\Property(property="name", type="integer", example="4"),
     *      ),
     *  ),
     *  @OA\Response(response=201, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Ram"),
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
     * @param  \App\Http\Requests\Ram\StoreRamRequest $request
     * 
     * @return JsonResponse
     */
    public function store(StoreRamRequest $request)
    {
        $ram = Ram::create($request->all());

        return $this->respondWithResource(new RamResource($ram), 'Thêm thành công', 201);
    }

    /**
     * @OA\Get(
     *  path="/rams/{id}",
     *  tags={"Ram"},
     *  summary="Find Ram By ID",
     *  operationId="showRam",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *  @OA\Response(response=200, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Ram"),
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
            $ram = Ram::findOrFail($id);
            return $this->respondWithResource(new RamResource($ram));
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * @OA\Put(
     *  path="/rams/{id}",
     *  tags={"Ram"},
     *  summary="Update Ram",
     *  operationId="updateRam",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer",)),
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"name", "_method"},
     *          @OA\Property(property="name", type="integer", example="4"),
     *      ),
     *  ),
     *  @OA\Response(response=200, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Ram"),
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
     * @param  \App\Http\Requests\Ram\UpdateRamRequest $request
     * @param  int  $id
     * 
     * @return JsonResponse
     */
    public function update(UpdateRamRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $ram = Ram::findOrFail($id);
            $ram->update($request->all());
            DB::commit();

            return $this->respondWithResource(new RamResource($ram), 'Sửa thành công');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Delete(
     *  path="/rams/{id}",
     *  tags={"Ram"},
     *  summary="Delete Ram By ID",
     *  operationId="destroyRam",
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
            $ram = Ram::findOrFail($id);
            $ram->delete();

            return $this->respondSuccess('Xoá thành công');
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * @OA\Get(
     *  path="/rams/excel/export",
     *  tags={"Ram"},
     *  summary="Export Excel Ram",
     *  operationId="exportRam",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Parameter(name="sort", in="query", required=true, description="sort by column: 0-id, 1-name, 2-created_at", @OA\Schema(type="integer", default="0", enum={0,1,2})),
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
            $fileName = 'bo-nho-' . now()->format('d-m-Y-his') . '.xlsx';

            return $this->excel->download(new RamExport($condition), $fileName);
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
}
