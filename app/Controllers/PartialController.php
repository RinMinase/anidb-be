<?php

namespace App\Controllers;

use TypeError;

use Illuminate\Http\Request;
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
   * @apiSuccess {UUID} data.uuid Partial ID
   * @apiSuccess {String} data.title Partial title
   * @apiSuccess {Number} data.id_priority Partial priority
   * @apiSuccess {UUID} data.id_catalogs Partial catalog
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "uuid": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *       "title": "Title",
   *       "id_priority": 2,
   *       "id_catalogs": "9ef81943-78f0-4d1c-a831-a59fb5af339c",
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index($uuid) {
    try {
      return response()->json([
        'data' => $this->partialRepository->get($uuid),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'The provided ID is invalid, or the item does not exist',
      ], 401);
    }
  }

  public function add(AddRequest $request): JsonResponse {
    try {
      $this->partialRepository->add($request->all());

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

  public function add_multiple(Request $request, $catalog_uuid): JsonResponse {
    try {
      $data = json_decode($request->get('data'));
      $count = $this->partialRepository->add_multiple($data, $catalog_uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'accepted' => $count,
          'total' => count($data),
        ],
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Catalog ID does not exist',
      ], 401);
    } catch (TypeError) {
      return response()->json([
        'status' => 400,
        'message' => 'Error in parsing request body',
      ], 401);
    }
  }

  public function edit(EditRequest $request, $uuid): JsonResponse {
    try {
      $body = $request->only('title', 'id_catalogs', 'id_priority');
      $this->partialRepository->edit($body, $uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Catalog ID does not exist',
      ], 401);
    } catch (TypeError) {
      return response()->json([
        'status' => 400,
        'message' => 'Error in parsing request body',
      ], 401);
    }
  }

  public function edit_multiple(Request $request): JsonResponse {
    try {
      $data = json_decode($request->get('data'));
      $count = $this->partialRepository->edit_multiple($data);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'accepted' => $count,
          'total' => count($data),
        ],
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'A Partial ID in the array does not exist',
      ], 401);
    } catch (TypeError) {
      return response()->json([
        'status' => 400,
        'message' => 'Error in parsing request body',
      ], 401);
    }
  }

  public function delete($uuid): JsonResponse {
    try {
      $this->partialRepository->delete($uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Partial ID does not exist',
      ], 401);
    }
  }
}
