<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PCInfoRepository;
use App\Repositories\PCSetupRepository;

use App\Requests\PC\AddEditInfoRequest;
use App\Requests\PC\AddEditSetupRequest;
use App\Requests\ImportRequest;

use App\Resources\DefaultResponse;
use App\Resources\PC\PCInfoSummaryResource;

class PCInfoController extends Controller {

  private PCInfoRepository $pcInfoRepository;
  private PCSetupRepository $pcSetupRepository;

  public function __construct(
    PCInfoRepository $pcInfoRepository,
    PCSetupRepository $pcSetupRepository
  ) {
    $this->pcInfoRepository = $pcInfoRepository;
    $this->pcSetupRepository = $pcSetupRepository;
  }

  /**
   * @OA\Get(
   *   tags={"PC"},
   *   path="/api/pc/infos",
   *   summary="Get All PC Info",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/PCInfoSummaryResource"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => PCInfoSummaryResource::collection($this->pcInfoRepository->get_all()),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"PC"},
   *   path="/api/pc/infos/{info_uuid}",
   *   summary="Get a PC Info",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="info_uuid",
   *     description="PC Info UUID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/PCInfoResource"),
   *           @OA\Property(
   *             property="stats",
   *             @OA\Property(property="totalSetupCost", type="integer", example=10000),
   *             @OA\Property(property="totalSetupCostFormat", type="string", example="10,000"),
   *             @OA\Property(property="totalSystemCost", type="integer", example=10000),
   *             @OA\Property(property="totalSystemCostFormat", type="string", example="10,000"),
   *             @OA\Property(property="totalPeripheralCost", type="integer", example=10000),
   *             @OA\Property(property="totalPeripheralCostFormat", type="string", example="10,000"),
   *             @OA\Property(property="highlightCpu", type="string", example="Sample CPU Name"),
   *             @OA\Property(property="highlightGpu", type="string", example="Sample GPU Name"),
   *             @OA\Property(property="highlightRam", type="string", example="Sample RAM Details"),
   *             @OA\Property(property="highlightStorage", type="string", example="Sample Storage Details"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($uuid): JsonResponse {
    $data = $this->pcInfoRepository->get($uuid);

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"PC"},
   *   path="/api/pc/infos",
   *   summary="Add a PC Info",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_info_id_owner"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_info_label"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_info_is_active"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_info_is_hidden"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditInfoRequest $request): JsonResponse {
    $this->pcInfoRepository->add(
      $request->only('id_owner', 'label', 'is_active', 'is_hidden'),
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"PC"},
   *   path="/api/pc/infos/{info_uuid}",
   *   summary="Edit a PC Info",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="info_uuid",
   *     description="PC Info UUID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_info_id_owner"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_info_label"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_info_is_active"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_info_is_hidden"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditInfoRequest $request, $uuid): JsonResponse {
    $this->pcInfoRepository->edit(
      $request->only('id_owner', 'label', 'is_active', 'is_hidden'),
      $uuid,
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"PC"},
   *   path="/api/pc/infos/{info_uuid}",
   *   summary="Delete a PC Info",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="info_uuid",
   *     description="PC Info UUID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function delete($uuid): JsonResponse {
    $this->pcInfoRepository->delete($uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"PC"},
   *   path="/api/pc/infos/import",
   *   summary="Import a JSON file to add (does not delete existing) data for PC infos table",
   *   security={{"token":{}}},
   *
   *   @OA\RequestBody(
   *     required=true,
   *     @OA\MediaType(
   *       mediaType="multipart/form-data",
   *       @OA\Schema(
   *         type="object",
   *         @OA\Property(property="file", type="string", format="binary"),
   *       ),
   *     ),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/DefaultImportSchema"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function import(ImportRequest $request): JsonResponse {
    $file = $request->file('file')->get();

    if (!is_json($file)) {
      throw new JsonParsingException();
    }

    $data = json_decode($file);
    $count = $this->pcInfoRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }

  public function duplicate($uuid): JsonResponse {
    $this->pcInfoRepository->duplicate($uuid);

    return DefaultResponse::success();
  }

  public function add_setup(AddEditSetupRequest $request, $uuid): JsonResponse {
    $this->pcSetupRepository->add($request->only('data'), $uuid);

    return DefaultResponse::success();
  }

  public function edit_setup(AddEditSetupRequest $request, $uuid): JsonResponse {
    $this->pcSetupRepository->edit($request->only('data'), $uuid);

    return DefaultResponse::success();
  }
}
