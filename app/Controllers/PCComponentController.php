<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PCComponentRepository;
use App\Requests\PC\AddEditComponentRequest;
use App\Requests\ImportRequest;
use App\Resources\DefaultResponse;

class PCComponentController extends Controller {

  private PCComponentRepository $pcComponentRepository;

  public function __construct(PCComponentRepository $pcComponentRepository) {
    $this->pcComponentRepository = $pcComponentRepository;
  }

  /**
   * @OA\Get(
   *   tags={"PC"},
   *   path="/api/pc/components",
   *   summary="Get All PC Components",
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
   *             @OA\Items(ref="#/components/schemas/PCComponent"),
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
      'data' => $this->pcComponentRepository->getAll(),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"PC"},
   *   path="/api/pc/components",
   *   summary="Add a PC Component",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_id_type"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_name"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_description"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_purchase_date"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_purchase_location"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_purchase_notes"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_is_onhand"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditComponentRequest $request): JsonResponse {
    $this->pcComponentRepository->add($request->only(
      'id_type',
      'name',
      'description',
      'price',
      'purchase_date',
      'purchase_location',
      'purchase_notes',
      'is_onhand',
    ));

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"PC"},
   *   path="/api/pc/components/{component_id}",
   *   summary="Edit a PC Component",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="component_id",
   *     description="Component ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_id_type"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_name"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_description"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_price"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_purchase_date"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_purchase_location"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_purchase_notes"),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_component_is_onhand"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditComponentRequest $request, $id): JsonResponse {
    $this->pcComponentRepository->edit(
      $request->only(
        'id_type',
        'name',
        'description',
        'price',
        'purchase_date',
        'purchase_location',
        'purchase_notes',
        'is_onhand'
      ),
      $id
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"PC"},
   *   path="/api/pc/components/{component_id}",
   *   summary="Delete a PC Component",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="component_id",
   *     description="Component ID",
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
  public function delete($id): JsonResponse {
    $this->pcComponentRepository->delete($id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"PC"},
   *   path="/api/pc/components/import",
   *   summary="Import a JSON file to add (does not delete existing) data for PC components table",
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
    $count = $this->pcComponentRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
