<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Exceptions\JsonParsingException;
use App\Repositories\GroupRepository;
use App\Requests\ImportRequest;
use App\Requests\Group\AddEditRequest;
use App\Resources\DefaultResponse;

class GroupController extends Controller {

  private GroupRepository $groupRepository;

  public function __construct(GroupRepository $groupRepository) {
    $this->groupRepository = $groupRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Group"},
   *   path="/api/groups",
   *   summary="Get All Groups",
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
   *             @OA\Items(ref="#/components/schemas/Group"),
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
      'data' => $this->groupRepository->getAll(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Group"},
   *   path="/api/groups/names",
   *   summary="Get All Group Names",
   *   security={{"token":{}, "api-key": {}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", type="array", @OA\Items(type="string")),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function getNames(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->groupRepository->getNames(),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Group"},
   *   path="/api/groups",
   *   summary="Add a Group",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/group_add_edit_name"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->groupRepository->add($request->only('name'));

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Group"},
   *   path="/api/groups/{group_id}",
   *   summary="Edit a Group",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="group_id",
   *     description="Group ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(ref="#/components/parameters/group_add_edit_name"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditRequest $request, $uuid): JsonResponse {
    $this->groupRepository->edit($request->only('name'), $uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"Group"},
   *   path="/api/groups/{group_id}",
   *   summary="Delete a Group",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="group_id",
   *     description="Group ID",
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
    $this->groupRepository->delete($uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Import - Archaic"},
   *   path="/api/archaic/import/groups",
   *   summary="Import a JSON file to seed data for groups table",
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
  public function import(ImportRequest $request) {
    $file = $request->file('file')->get();

    if (!is_json($file)) {
      throw new JsonParsingException();
    }

    $data = json_decode($file);
    $count = $this->groupRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
