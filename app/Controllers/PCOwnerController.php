<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PCOwnerRepository;
use App\Requests\ImportRequest;
use App\Requests\PC\AddEditOwnerRequest;
use App\Requests\PC\GetOwnersRequest;
use App\Resources\DefaultResponse;

class PCOwnerController extends Controller {

  private PCOwnerRepository $pcOwnerRepository;

  public function __construct(PCOwnerRepository $pcOwnerRepository) {
    $this->pcOwnerRepository = $pcOwnerRepository;
  }

  /**
   * @OA\Get(
   *   tags={"PC"},
   *   path="/api/pc/owners",
   *   summary="Get All PC Owners",
   *   security={{"token":{}, "api-key": {}}},
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
   *             @OA\Items(
   *               @OA\Property(
   *                 property="uuid",
   *                 type="string",
   *                 format="uuid",
   *                 example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *               ),
   *               @OA\Property(property="name", type="string"),
   *               @OA\Property(
   *                 property="infos",
   *                 type="array",
   *                 @OA\Items(ref="#/components/schemas/PCInfoSummaryResource"),
   *               ),
   *             ),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(GetOwnersRequest $request): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->pcOwnerRepository->getAll($request->only('show_hidden')),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"PC"},
   *   path="/api/pc/owners/{owner_uuid}",
   *   summary="Get PC Owner",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="owner_uuid",
   *     description="Owner UUID",
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
   *           @OA\Property(property="data", ref="#/components/schemas/PCOwner"),
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
      'data' => $this->pcOwnerRepository->get($uuid),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"PC"},
   *   path="/api/pc/owners",
   *   summary="Add a PC Owner",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_owner_name"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditOwnerRequest $request): JsonResponse {
    $this->pcOwnerRepository->add($request->only('name'));

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"PC"},
   *   path="/api/pc/owners/{owner_uuid}",
   *   summary="Edit a PC Owner",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="owner_uuid",
   *     description="Owner UUID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(ref="#/components/parameters/pc_add_edit_owner_name"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditOwnerRequest $request, $uuid): JsonResponse {
    $this->pcOwnerRepository->edit(
      $request->only('name'),
      $uuid
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"PC"},
   *   path="/api/pc/owners/{owner_uuid}",
   *   summary="Delete a PC Owner",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="owner_uuid",
   *     description="Owner UUID",
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
    $this->pcOwnerRepository->delete($uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"PC"},
   *   path="/api/pc/owners/import",
   *   summary="Import a JSON file to add (does not delete existing) data for PC owners table",
   *   security={{"token":{}, "api-key": {}}},
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
    $count = $this->pcOwnerRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
