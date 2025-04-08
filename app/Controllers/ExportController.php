<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Enums\ExportTypesEnum;
use App\Repositories\ExportRepository;
use App\Resources\DefaultResponse;

class ExportController extends Controller {

  private ExportRepository $exportRepository;

  public function __construct(ExportRepository $exportRepository) {
    $this->exportRepository = $exportRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Import"},
   *   path="/api/exports",
   *   summary="Get All Exports",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/Export"),
   *           ),
   *         ),
   *       }
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->exportRepository->get_all(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Import"},
   *   path="/api/exports/{export_id}",
   *   summary="Get Single Export",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="export_id",
   *     description="Export UUID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="string",
   *             format="url",
   *             example="https://download.url?expires=00000&signature=<signature>",
   *           ),
   *         ),
   *       }
   *     )
   *   ),
   *   @OA\Response(response=400, ref="#/components/responses/ExportFileIncompleteResponse"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function generate_download_url($uuid): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->exportRepository->get_download_url($uuid),
    ]);
  }

  // No API Docs for actual download link
  public function download(string $path): BinaryFileResponse {
    $data = $this->exportRepository->download($path);

    return response()->download($data['file'], $data['filename'], $data['headers']);
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/exports/json",
   *   summary="Generate JSON Export",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=400, ref="#/components/responses/ExportFileIncompleteResponse"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function generate_json(): JsonResponse {
    ExportRepository::generate_export(ExportTypesEnum::JSON, false);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/exports/sql",
   *   summary="Generate SQL Export",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=400, ref="#/components/responses/ExportFileIncompleteResponse"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function generate_sql(): JsonResponse {
    ExportRepository::generate_export(ExportTypesEnum::SQL, false);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/exports/xlsx",
   *   summary="Generate XLSX (Excel File) Export",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=400, ref="#/components/responses/ExportFileIncompleteResponse"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function generate_xlsx(): JsonResponse {
    ExportRepository::generate_export(ExportTypesEnum::XLSX, false);

    return DefaultResponse::success();
  }
}
