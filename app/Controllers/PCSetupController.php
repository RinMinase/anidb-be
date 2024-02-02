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
   * @OA\Get(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups",
   *   summary="Get All PC Setups",
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
   *             @OA\Items(ref="#/components/schemas/PCSetup"),
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
    $setups = $this->pcSetupRepository->getAll();

    return DefaultResponse::success(null, [
      'data' => $setups,
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/pc-setups/import",
   *   summary="Import a JSON file to seed data for PC Setups table",
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
  public function import(ImportRequest $request) {
    $file = json_decode($request->file('file')->get());
    $count = $this->pcSetupRepository->import($file);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($file),
      ],
    ]);
  }
}
