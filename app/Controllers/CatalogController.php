<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\CatalogRepository;
use App\Requests\Catalog\AddRequest;
use App\Resources\Catalog\CatalogCollection;
use App\Resources\Catalog\CatalogPartialCollection;

class CatalogController extends Controller {

  private CatalogRepository $catalogRepository;

  public function __construct(CatalogRepository $catalogRepository) {
    $this->catalogRepository = $catalogRepository;
  }


  /**
   * @api {get} /api/catalogs Retrieve Catalogs
   * @apiName RetrieveCatalog
   * @apiGroup Catalog
   *
   * @apiHeader {String} token User login token
   *
   * @apiSuccess {Object[]} data Catalog data
   * @apiSuccess {UUID} data.id Catalog ID
   * @apiSuccess {Number} data.year Catalog year
   * @apiSuccess {String='Winter','Spring','Summer','Fall'} date.season Catalog season
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *         "year": 2010,
   *         "season": "Winter",
   *       }, {
   *         "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *         "year": null,
   *         "season": "null",
   *       }, {
   *         "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *         "year": 2015,
   *         "season": "Winter",
   *       }, { ... }
   *     ]
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => CatalogCollection::collection($this->catalogRepository->getAll()),
    ]);
  }

  public function add(AddRequest $request): JsonResponse {
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

  public function edit(Request $request, $id): JsonResponse {
    if (!isset($id)) {
      return $this->groupEdit($request);
    } else {
      return $this->singleEdit($request, $id);
    }
  }

  public function delete($id): JsonResponse {
    try {
      return response()->json([
        'data' => $this->catalogRepository->delete($id),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Catalog ID does not exist',
      ], 401);
    }
  }

  private function singleEdit(Request $request, $id): JsonResponse {
    return response()->json([
      'data' => $this->catalogRepository->edit($request->all(), $id),
    ]);
  }

  private function groupEdit(Request $request): JsonResponse {
    return response()->json([
      'data' => [],
    ]);
  }
}
