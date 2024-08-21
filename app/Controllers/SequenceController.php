<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Exceptions\JsonParsingException;
use App\Repositories\SequenceRepository;
use App\Requests\ImportRequest;
use App\Requests\Sequence\AddEditRequest;
use App\Resources\DefaultResponse;

class SequenceController extends Controller {

  private SequenceRepository $sequenceRepository;

  public function __construct(SequenceRepository $sequenceRepository) {
    $this->sequenceRepository = $sequenceRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Sequence"},
   *   path="/api/sequences",
   *   summary="Get All Sequences",
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
   *             @OA\Items(ref="#/components/schemas/Sequence"),
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
      'data' => $this->sequenceRepository->getAll(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Sequence"},
   *   path="/api/sequences/{sequence_id}",
   *   summary="Get Sequence",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="sequence_id",
   *     description="Sequence ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/Sequence"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($id): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->sequenceRepository->get($id),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Sequence"},
   *   path="/api/sequences",
   *   summary="Add a Sequence",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/sequence_add_edit_title"),
   *   @OA\Parameter(ref="#/components/parameters/sequence_add_edit_date_from"),
   *   @OA\Parameter(ref="#/components/parameters/sequence_add_edit_date_to"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->sequenceRepository->add(
      $request->only('title', 'date_from', 'date_to'),
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Sequence"},
   *   path="/api/sequences/{sequence_id}",
   *   summary="Edit a Sequence",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="sequence_id",
   *     description="Sequence ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *   @OA\Parameter(ref="#/components/parameters/sequence_add_edit_title"),
   *   @OA\Parameter(ref="#/components/parameters/sequence_add_edit_date_from"),
   *   @OA\Parameter(ref="#/components/parameters/sequence_add_edit_date_to"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditRequest $request, $id): JsonResponse {
    $this->sequenceRepository->edit(
      $request->only('title', 'date_from', 'date_to'),
      $id,
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"Sequence"},
   *   path="/api/sequences/{sequence_id}",
   *   summary="Delete a Sequence",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="sequence_id",
   *     description="Sequence ID",
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
    $this->sequenceRepository->delete($id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/sequences/import",
   *   summary="Import a JSON file to seed data for sequences table",
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
    $count = $this->sequenceRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
