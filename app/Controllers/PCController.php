<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PCComponentRepository;
use App\Repositories\PCInfoRepository;
use App\Repositories\PCOwnerRepository;
use App\Repositories\PCSetupRepository;

use App\Requests\ImportRequest;
use App\Resources\DefaultResponse;

class PCController extends Controller {

  private PCOwnerRepository $pcOwnerRepository;
  private PCComponentRepository $pcComponentRepository;
  private PCInfoRepository $pcInfoRepository;
  private PCSetupRepository $pcSetupRepository;

  public function __construct(
    PCOwnerRepository $pcOwnerRepository,
    PCComponentRepository $pcComponentRepository,
    PCInfoRepository $pcInfoRepository,
    PCSetupRepository $pcSetupRepository,
  ) {
    $this->pcOwnerRepository = $pcOwnerRepository;
    $this->pcComponentRepository = $pcComponentRepository;
    $this->pcInfoRepository = $pcInfoRepository;
    $this->pcSetupRepository = $pcSetupRepository;
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/pc/import",
   *   summary="Import a JSON file to add (does not delete existing) data for all PC-related tables",
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
   *           @OA\Property(
   *             property="data",
   *             @OA\Property(property="owners", ref="#/components/schemas/DefaultImportSchema"),
   *             @OA\Property(property="components", ref="#/components/schemas/DefaultImportSchema"),
   *             @OA\Property(property="infos", ref="#/components/schemas/DefaultImportSchema"),
   *             @OA\Property(property="setups", ref="#/components/schemas/DefaultImportSchema"),
   *           ),
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

    $countOwners = 0;
    $totalOwners = 0;
    if (isset($data->owners)) {
      $totalOwners = count($data->owners);
      $countOwners = $this->pcOwnerRepository->import($data->owners);
    }

    $countComponents = 0;
    $totalComponents = 0;
    if (isset($data->components)) {
      $totalComponents = count($data->components);
      $countComponents = $this->pcComponentRepository->import($data->components);
    }

    $countInfos = 0;
    $totalInfos = 0;
    if (isset($data->infos)) {
      $totalInfos = count($data->infos);
      $countInfos = $this->pcInfoRepository->import($data->infos);
    }

    $countSetups = 0;
    $totalSetups = 0;
    if (isset($data->setups)) {
      $totalSetups = count($data->setups);
      $countSetups = $this->pcSetupRepository->import($data->setups);
    }


    return DefaultResponse::success(null, [
      'data' => [
        'owners' => [
          'acceptedImports' => $countOwners,
          'totalJsonEntries' => $totalOwners,
        ],
        'components' => [
          'acceptedImports' => $countComponents,
          'totalJsonEntries' => $totalComponents,
        ],
        'infos' => [
          'acceptedImports' => $countInfos,
          'totalJsonEntries' => $totalInfos,
        ],
        'setups' => [
          'acceptedImports' => $countSetups,
          'totalJsonEntries' => $totalSetups,
        ],
      ],
    ]);
  }
}
