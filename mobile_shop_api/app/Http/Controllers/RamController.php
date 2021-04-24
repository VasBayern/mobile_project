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
     *  @OA\Response(response=200, description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object", ref="#/components/schemas/Ram"),
     *     )
     *  ),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *)
     **/
    /**
     * Display a listing of the resource.
     * 
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $rams = Ram::paginate(10);

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
     *  @OA\Response(response=200, description="Success"),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *)
     **/
    public function export()
    {
        try {
            $fileName = 'bo-nho-' . now()->format('dmY-his') . '.xlsx';

            return $this->excel->download(new RamExport(), $fileName);
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
}
