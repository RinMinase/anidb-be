<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PCComponentTypeRepository;
use App\Requests\PC\AddEditComponentTypeRequest;
use App\Resources\DefaultResponse;

class PCComponentTypeController extends Controller {

  private PCComponentTypeRepository $pcComponentTypeRepository;

  public function __construct(PCComponentTypeRepository $pcComponentTypeRepository) {
    $this->pcComponentTypeRepository = $pcComponentTypeRepository;
  }

  /**
   * @OA\Get(
   *   tags={"PC"},
   *   path="/api/pc/types",
   *   summary="Get All PC Component Types",
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
   *             @OA\Items(ref="#/components/schemas/PCComponentType"),
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
      'data' => $this->pcComponentTypeRepository->getAll(),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"PC"},
   *   path="/api/pc/types",
   *   summary="Add a PC Component Type",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_type_type"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_type_name"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_type_is_peripheral"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditComponentTypeRequest $request): JsonResponse {
    $this->pcComponentTypeRepository->add($request->only('type', 'name', 'is_peripheral'));

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"PC"},
   *   path="/api/pc/types/{type_id}",
   *   summary="Edit a PC Component Type",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="type_id",
   *     description="Type ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_type_type"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_type_name"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_type_is_peripheral"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditComponentTypeRequest $request, $id): JsonResponse {
    $this->pcComponentTypeRepository->edit($request->only('type', 'name', 'is_peripheral'), $id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"PC"},
   *   path="/api/pc/types/{type_id}",
   *   summary="Delete a PC Component Type",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="type_id",
   *     description="Type ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function delete($uuid): JsonResponse {
    $this->pcComponentTypeRepository->delete($uuid);

    return DefaultResponse::success();
  }
}
