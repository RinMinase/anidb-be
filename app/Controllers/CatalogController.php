<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\CatalogRepository;
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
   * @apiSuccess {String} data.description Catalog description
   * @apiSuccess {Number} data.order Defined order of catalog entry
   * @apiSuccess {Number} data.year Catalog year
   * @apiSuccess {String='Winter','Spring','Summer','Fall'} date.season Catalog season
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *         "description": "description",
   *         "order": 1,
   *         "year": 2010,
   *         "season": "Winter",
   *       }, {
   *         "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *         "description": "TBA",
   *         "order": 2,
   *         "year": null,
   *         "season": "null",
   *       }, {
   *         "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *         "description": "another description",
   *         "order": null,
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


  /**
   * @api {get} /api/catalogs/:id Retrieve Catalogs
   * @apiName RetrieveCatalog
   * @apiGroup Catalog
   *
   * @apiHeader {String} token User login token
   * @apiParam {String} id Catalog UUID.
   *
   * @apiSuccess {Object[]} data Partials data in Catalog
   * @apiSuccess {UUID} data.id Partial ID
   * @apiSuccess {String} data.title Partial title
   * @apiSuccess {Number} data.priority Partial priority
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *         "title": "Title",
   *         "priority": "High",
   *       }, {
   *         "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *         "title": "Another Title",
   *         "priority": "Normal",
   *       }, { ... }
   *     ]
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function get($uuid): JsonResponse {
    try {
      return response()->json([
        'data' => CatalogPartialCollection::collection(
          $this->catalogRepository->get($uuid)
        ),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'The provided ID is invalid, or the item does not exist',
      ], 401);
    }
  }

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
