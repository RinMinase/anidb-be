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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function generate_download_url($uuid): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->exportRepository->get_download_url($uuid),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Import"},
   *   path="/api/local/temp/{filename}",
   *   summary="Download Export File",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="filename",
   *     description="UUID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(name="expires", in="query", required=true, @OA\Schema(type="string")),
   *   @OA\Parameter(name="signature", in="query", required=true, @OA\Schema(type="string")),
   *
   *   @OA\Response(response=200, description="OK", @OA\Schema(type="file")),
   *   @OA\Response(response=400, ref="#/components/responses/ExportFileIncompleteResponse"),
   *   @OA\Response(
   *     response=403,
   *     description="Forbidden",
   *     @OA\JsonContent(
   *       example={"status": 403, "message": "Invalid signature provided"},
   *       @OA\Property(property="status", type="integer", format="int32"),
   *       @OA\Property(property="message", type="string"),
   *     ),
   *   ),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/ExportDownloadFailedResponse"),
   * )
   */
  public function download(string $uuid): BinaryFileResponse {
    $data = $this->exportRepository->download($uuid);

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

/**
 * @OA\Response(
 *   response="ExportDownloadFailedResponse",
 *   description="Other Error Responses",
 *   @OA\JsonContent(
 *     examples={
 *       @OA\Examples(
 *         example="ZipFileProcessErrorExample",
 *         ref="#/components/examples/ZipFileProcessErrorExample",
 *       ),
 *       @OA\Examples(
 *         example="BasicFailedExample",
 *         summary="Basic Failed Error",
 *         value={"status": 500, "message": "Failed"},
 *       ),
 *     },
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class ExportDownloadFailedResponse {
}
