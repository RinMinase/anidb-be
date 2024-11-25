<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PCInfoRepository;
use App\Requests\PC\AddEditInfoRequest;
use App\Resources\DefaultResponse;
use App\Resources\PC\PCInfoResource;
use App\Resources\PC\PCInfoSummaryResource;

class PCInfoController extends Controller {

  private PCInfoRepository $pcInfoRepository;

  public function __construct(PCInfoRepository $pcInfoRepository) {
    $this->pcInfoRepository = $pcInfoRepository;
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
   *             @OA\Items(ref="#/components/schemas/PCInfo"),
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
      'data' => PCInfoSummaryResource::collection($this->pcInfoRepository->getAll()),
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
   *           @OA\Property(property="data", ref="#/components/schemas/PCInfo"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($uuid): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => new PCInfoResource($this->pcInfoRepository->get($uuid)),
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
}
