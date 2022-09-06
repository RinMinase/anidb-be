<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\CatalogRepository;
use App\Resources\Catalog\CatalogCollection;
use App\Resources\Partial\PartialCollection;

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


  /**
   * @api {get} /api/partials/:uuid Retrieve Partials in Catalog
   * @apiName RetrievePartials
   * @apiGroup Partial
   *
   * @apiHeader {String} token User login token
   * @apiParam {String} uuid Catalog UUID.
   *
   * @apiSuccess {Object} stats Catalog information
   * @apiSuccess {Number} stats.year Catalog year
   * @apiSuccess {String='Winter','Spring','Summer','Fall'} stats.season Catalog season
   * @apiSuccess {Object[]} data Partials data in Catalog
   * @apiSuccess {UUID} data.id Partial ID
   * @apiSuccess {String} data.title Partial title
   * @apiSuccess {Number} data.priority Partial priority
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       data: [
   *         {
   *           "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *           "title": "Title",
   *           "priority": "High",
   *         }, {
   *           "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *           "title": "Another Title",
   *           "priority": "Normal",
   *         }, { ... }
   *       ],
   *       stats: {
   *         "year": 2020,
   *         "season": "Winter"
   *       }
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
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
