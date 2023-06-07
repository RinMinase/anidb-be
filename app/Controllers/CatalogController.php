<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\CatalogRepository;

use App\Resources\Catalog\CatalogCollection;

use App\Resources\DefaultResponse;

class CatalogController extends Controller {

  private CatalogRepository $catalogRepository;

  public function __construct(CatalogRepository $catalogRepository) {
    $this->catalogRepository = $catalogRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Catalog"},
   *   path="/api/catalogs",
   *   summary="Get All Catalogs",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/CatalogCollection"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => CatalogCollection::collection($this->catalogRepository->getAll()),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Catalog"},
   *   path="/api/catalogs/{catalog_id}",
   *   summary="Get Partials in Catalog",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="catalog_id",
   *     description="Catalog ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(property="data", ref="#/components/schemas/PartialCollection"),
   *       @OA\Property(property="stats", ref="#/components/schemas/Catalog"),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($uuid) {
    return response()->json($this->catalogRepository->get($uuid));
  }

  /**
   * @OA\Post(
   *   tags={"Catalog"},
   *   path="/api/catalogs",
   *   summary="Add a Catalog",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="year",
   *     in="query",
   *     required=true,
   *     example="2020",
   *     @OA\Schema(ref="#/components/schemas/YearSchema"),
   *   ),
   *   @OA\Parameter(
   *     name="season",
   *     in="query",
   *     required=true,
   *     example="Winter",
   *     @OA\Schema(type="string", enum={"Winter", "Spring", "Summer", "Fall"}),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(Request $request): JsonResponse {
    $this->catalogRepository->add($request->only('year', 'season'));

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Catalog"},
   *   path="/api/catalogs/{catalog_id}",
   *   summary="Edit a Catalog",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="catalog_id",
   *     description="Catalog ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(
   *     name="year",
   *     in="query",
   *     required=true,
   *     example="2020",
   *     @OA\Schema(ref="#/components/schemas/YearSchema"),
   *   ),
   *   @OA\Parameter(
   *     name="season",
   *     in="query",
   *     required=true,
   *     example="Winter",
   *     @OA\Schema(type="string", enum={"Winter", "Spring", "Summer", "Fall"}),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(Request $request, $id): JsonResponse {
    $this->catalogRepository->edit($request->only('year', 'season'), $id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"Catalog"},
   *   path="/api/catalogs/{catalog_id}",
   *   summary="Delete a Catalog",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="catalog_id",
   *     description="Catalog ID",
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
    $this->catalogRepository->delete($uuid);

    return DefaultResponse::success();
  }
}
