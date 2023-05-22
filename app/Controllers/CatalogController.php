<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\CatalogRepository;
use App\Resources\Catalog\CatalogCollection;

class CatalogController extends Controller {

  private CatalogRepository $catalogRepository;

  public function __construct(CatalogRepository $catalogRepository) {
    $this->catalogRepository = $catalogRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Catalog"},
   *   path="/api/catalogs",
   *   summary="Get All Catalog",
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
   *       @OA\Property(
   *         property="stats",
   *         type="object",
   *         ref="#/components/schemas/Catalog",
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function get($uuid) {
    try {
      return response()->json($this->catalogRepository->get($uuid));
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'The provided ID is invalid, or the item does not exist',
      ], 401);
    }
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
   *     @OA\Schema(type="integer", format="int32"),
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
   * )
   */
  public function add(Request $request): JsonResponse {
    try {
      $this->catalogRepository->add($request->all());

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
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
   *     @OA\Schema(type="integer", format="int32"),
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
   * )
   */
  public function edit(Request $request, $id): JsonResponse {
    try {
      $this->catalogRepository->edit($request->except(['_method']), $id);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Catalog ID does not exist',
      ], 401);
    }
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
   * )
   */
  public function delete($uuid): JsonResponse {
    try {
      $this->catalogRepository->delete($uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Catalog ID does not exist',
      ], 401);
    }
  }
}
