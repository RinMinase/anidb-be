<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\CatalogRepository;

use App\Requests\Catalog\AddEditRequest;

use App\Resources\Catalog\CatalogResource;
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
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/CatalogResource"),
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
      'data' => CatalogResource::collection($this->catalogRepository->getAll()),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Catalog"},
   *   path="/api/catalogs/{catalog_id}/partials",
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
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/PartialResource"),
   *           ),
   *           @OA\Property(property="stats", ref="#/components/schemas/CatalogResource"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($uuid) {
    $data = $this->catalogRepository->get($uuid);

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Catalog"},
   *   path="/api/catalogs",
   *   summary="Add a Catalog",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/catalog_add_edit_season"),
   *   @OA\Parameter(ref="#/components/parameters/catalog_add_edit_year"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->catalogRepository->add($request->only('season', 'year'));

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
   *   @OA\Parameter(ref="#/components/parameters/catalog_add_edit_season"),
   *   @OA\Parameter(ref="#/components/parameters/catalog_add_edit_year"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditRequest $request, $id): JsonResponse {
    $this->catalogRepository->edit($request->only('season', 'year'), $id);

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
