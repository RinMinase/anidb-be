<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\PartialRepository;
use App\Requests\Partial\AddRequest;
use App\Requests\Partial\EditRequest;
use App\Resources\Partial\PartialCollection;

class PartialController extends Controller {

  private PartialRepository $partialRepository;

  public function __construct(PartialRepository $partialRepository) {
    $this->partialRepository = $partialRepository;
  }


  /**
   * @api {get} /api/partials/:uuid Retrieve Partials in Catalog
   * @apiName RetrievePartials
   * @apiGroup Partial
   *
   * @apiHeader {String} token User login token
   * @apiParam {String} uuid Catalog UUID.
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
  public function index($uuid) {
    try {
      return response()->json([
        'data' => PartialCollection::collection(
          $this->partialRepository->getAll($uuid)
        ),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'The provided ID is invalid, or the item does not exist',
      ], 401);
    }
  }

  public function add(AddRequest $request): JsonResponse {
    return response()->json([
      'data' => $this->partialRepository->add($request->all()),
    ]);
  }

  public function edit(EditRequest $request, $id): JsonResponse {
    return response()->json([
      'data' => $this->partialRepository->edit($request->all(), $id),
    ]);
  }

  public function delete($id): JsonResponse {
    try {
      return response()->json([
        'data' => $this->partialRepository->delete($id),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Partial ID does not exist',
      ], 401);
    }
  }
}
