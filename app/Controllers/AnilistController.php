<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Repositories\AnilistRepository;

use App\Resources\Anilist\AnilistSearchResource;
use App\Resources\Anilist\AnilistTitleResource;
use App\Resources\DefaultResponse;

class AnilistController extends Controller {

  private AnilistRepository $anilistRepository;

  public function __construct(AnilistRepository $anilistRepository) {
    $this->anilistRepository = $anilistRepository;
  }

  /**
   * @OA\Get(
   *   tags={"AniList"},
   *   path="/api/anilist/title/{title_id}",
   *   summary="Retrieve Title Information",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="title_id",
   *     description="Title ID",
   *     in="path",
   *     required=true,
   *     example="101280",
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
   *           @OA\Property(
   *             property="data",
   *             ref="#/components/schemas/AnilistTitleResource",
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=429, ref="#/components/responses/AnilistRateLimitErrorResponse"),
   *   @OA\Response(response=500, ref="#/components/responses/AnilistOtherErrorResponse"),
   *   @OA\Response(response=503, ref="#/components/responses/AnilistConnectionErrorResponse"),
   * )
   */
  public function get($id = 101280): JsonResponse {
    $data = $this->anilistRepository->get($id);

    $data = $data['Media'];

    return DefaultResponse::success(null, [
      'data' => new AnilistTitleResource($data),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"AniList"},
   *   path="/api/anilist/search",
   *   summary="Query Titles",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="query",
   *     description="Title Search String",
   *     in="query",
   *     required=true,
   *     example="tensei",
   *     @OA\Schema(type="string"),
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
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/AnilistSearchResource"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=429, ref="#/components/responses/AnilistRateLimitErrorResponse"),
   *   @OA\Response(response=500, ref="#/components/responses/AnilistOtherErrorResponse"),
   *   @OA\Response(response=503, ref="#/components/responses/AnilistConnectionErrorResponse"),
   * )
   */
  public function search(Request $request): JsonResponse {
    $data = $this->anilistRepository->search($request->only('query'));
    $data = collect($data['Page']['media']);

    return DefaultResponse::success(null, [
      'data' => AnilistSearchResource::collection($data),
    ]);
  }
}

/**
 * @OA\Response(
 *   response="AnilistOtherErrorResponse",
 *   description="Other Error Responses",
 *   @OA\JsonContent(
 *     examples={
 *       @OA\Examples(
 *         example="AnilistConfigErrorExample",
 *         ref="#/components/examples/AnilistConfigErrorExample",
 *       ),
 *       @OA\Examples(
 *         example="AnilistParsingErrorExample",
 *         ref="#/components/examples/AnilistParsingErrorExample",
 *       ),
 *     },
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class AnilistOtherErrorResponse {
}
