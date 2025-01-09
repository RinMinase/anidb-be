<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PCSetupRepository;
use App\Requests\ImportRequest;
use App\Resources\DefaultResponse;

class PCSetupController extends Controller {

  private PCSetupRepository $pcSetupRepository;

  public function __construct(PCSetupRepository $pcSetupRepository) {
    $this->pcSetupRepository = $pcSetupRepository;
  }

  /**
   * @OA\Post(
   *   tags={"PC"},
   *   path="/api/pc/setups/import",
   *   summary="Import a JSON file to add (does not delete existing) data for PC setups table",
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
    $count = $this->pcSetupRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
