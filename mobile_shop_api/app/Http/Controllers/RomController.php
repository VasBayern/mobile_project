<?php

namespace App\Http\Controllers;

use App\Exports\RomExport;
use App\Http\Requests\Rom\StoreRomRequest;
use App\Http\Requests\Rom\UpdateRomRequest;
use App\Http\Resources\RomResource;
use App\Models\Rom;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class RomController extends Controller
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
     *  path="/roms",
     *  tags={"Rom"},
     *  summary="Get All Rom",
     *  operationId="getRoms",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     *  @OA\Response(response=200, description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object", ref="#/components/schemas/Rom"),
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
            $roms = Rom::paginate(10);

            return $this->respondWithResourceCollection(RomResource::collection($roms));
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Post(
     *  path="/roms",
     *  tags={"Rom"},
     *  summary="Add Rom",
     *  operationId="storeRom",
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
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Rom"),
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
     * @param  \App\Http\Requests\Rom\StoreRomRequest $request
     * 
     * @return JsonResponse
     */
    public function store(StoreRomRequest $request)
    {
        $rom = Rom::create($request->all());

        return $this->respondWithResource(new RomResource($rom), 'Thêm thành công', 201);
    }

    /**
     * @OA\Get(
     *  path="/roms/{id}",
     *  tags={"Rom"},
     *  summary="Find Rom By ID",
     *  operationId="showRom",
     *  security={
     *      {"bearerAuth": {}}
     *  },
     * 
     *  @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *  @OA\Response(response=200, description="Success",
     *      @OA\JsonContent(
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Rom"),
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
            $rom = Rom::findOrFail($id);
            return $this->respondWithResource(new RomResource($rom));
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * @OA\Put(
     *  path="/roms/{id}",
     *  tags={"Rom"},
     *  summary="Update Rom",
     *  operationId="updateRom",
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
     *          @OA\Property(property="data", type="object", ref="#/components/schemas/Rom"),
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
     * @param  \App\Http\Requests\Rom\UpdateRomRequest $request
     * @param  int  $id
     * 
     * @return JsonResponse
     */
    public function update(UpdateRomRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $rom = Rom::findOrFail($id);
            $rom->update($request->all());
            DB::commit();

            return $this->respondWithResource(new RomResource($rom), 'Sửa thành công');
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }

    /**
     * @OA\Delete(
     *  path="/roms/{id}",
     *  tags={"Rom"},
     *  summary="Delete Rom By ID",
     *  operationId="destroyRom",
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
            $rom = Rom::findOrFail($id);
            $rom->delete();

            return $this->respondSuccess('Xoá thành công');
        } catch (Exception $exception) {
            return $this->respondNotFound($exception, 'ID không tồn tại!');
        }
    }

    /**
     * @OA\Get(
     *  path="/roms/excel/export",
     *  tags={"Rom"},
     *  summary="Export Excel Rom",
     *  operationId="exportRom",
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
            $fileName = 'dung-luong-' . now()->format('dmY-his') . '.xlsx';

            return $this->excel->download(new RomExport(), $fileName);
        } catch (Exception $exception) {
            return $this->respondError($exception, 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
}
