<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

use App\Models\MALEntry;
use App\Models\MALSearch;

use App\Exceptions\Mal\ConfigDisabledException;
use App\Exceptions\Mal\ConfigException;
use App\Exceptions\Mal\ConnectionException;

use App\Resources\DefaultResponse;

class MalController extends Controller {

  protected $scrapeURI;

  public function __construct() {
    $this->scrapeURI = config('app.scraper.base_uri');
  }

  /**
   * @OA\Get(
   *   tags={"MAL"},
   *   path="/api/mal/title/{title_id}",
   *   summary="Retrieve Title Information",
   *   security={{"token":{}}},
   *   deprecated=true,
   *
   *   @OA\Parameter(
   *     name="title_id",
   *     description="Title ID",
   *     in="path",
   *     required=true,
   *     example="39535",
   *     @OA\Schema(type="integer", format="int64"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/MALEntry"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/MalOtherErrorResponse"),
   *   @OA\Response(response=503, ref="#/components/responses/MalConnectionResponse"),
   * )
   */
  public function get($id = 37430): JsonResponse {
    try {

      if (config('app.scraper.disabled')) throw new ConfigDisabledException();
      if (!config('app.scraper.base_uri')) throw new ConfigException();

      $response = Http::get($this->scrapeURI . '/anime/' . $id);

      if ($response->status() >= 500) {
        // Temporary response, will be changed to backup scraper
        throw new ConnectionException();
      }

      $data = $response->body();
      $data = MALEntry::parse(new Crawler($data))->get();

      return DefaultResponse::success(null, [
        'data' => $data,
      ]);
    } catch (Exception) {
      throw new ConnectionException();
    }
  }

  /**
   * @OA\Get(
   *   tags={"MAL"},
   *   path="/api/mal/search/{query_string}",
   *   summary="Query Titles",
   *   security={{"token":{}}},
   *   deprecated=true,
   *
   *   @OA\Parameter(
   *     name="query_string",
   *     description="Title Search String",
   *     in="path",
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
   *             @OA\Items(ref="#/components/schemas/MALSearch"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/MalOtherErrorResponse"),
   *   @OA\Response(response=503, ref="#/components/responses/MalConnectionResponse"),
   * )
   */
  public function search($query): JsonResponse {
    try {

      if (config('app.scraper.disabled')) throw new ConfigDisabledException();
      if (!config('app.scraper.base_uri')) throw new ConfigException();

      $response = Http::get($this->scrapeURI . '/anime.php?q=' . urldecode($query));

      if ($response->status() >= 500) {
        // Temporary response, will be changed to backup scraper
        throw new ConnectionException();
      }

      $data = $response->body();
      $data = MALSearch::parse(new Crawler($data))->get();

      return DefaultResponse::success(null, [
        'data' => $data,
      ]);
    } catch (Exception) {
      throw new ConnectionException();
    }
  }
}

/**
 * @OA\Response(
 *   response="MalOtherErrorResponse",
 *   description="Other Error Responses",
 *   @OA\JsonContent(
 *     examples={
 *       @OA\Examples(
 *         example="MalConfigErrorExample",
 *         ref="#/components/examples/MalConfigErrorExample",
 *       ),
 *       @OA\Examples(
 *         example="MalConfigDisabledErrorExample",
 *         ref="#/components/examples/MalConfigDisabledErrorExample",
 *       ),
 *     },
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class MalScraperConfigErrorResponse {
}
