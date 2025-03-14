<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\JsonResponse;

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
   *   security={{"token":{}}},
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
   *   security={{"token":{}}},
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
  public function generate_download_url($uuid): string {
    return $this->exportRepository->get_download_url($uuid);
  }

  // No API Docs for actual download link
  public function download(string $path): BinaryFileResponse {
    $data = $this->exportRepository->download($path);

    return response()->download($data['file'], $data['filename'], $data['headers']);
  }
}
