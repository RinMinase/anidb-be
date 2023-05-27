<?php

namespace App\Controllers;

use TypeError;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\PartialRepository;

use App\Requests\Partial\AddRequest;
use App\Requests\Partial\EditRequest;

use App\Resources\DefaultResponse;
use App\Resources\ErrorResponse;

class PartialController extends Controller {

  private PartialRepository $partialRepository;

  public function __construct(PartialRepository $partialRepository) {
    $this->partialRepository = $partialRepository;
  }

  /**
   * @OA\Post(
   *   tags={"Catalog"},
   *   path="/api/partials",
   *   summary="Add a Partial Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="title",
   *     in="query",
   *     required=true,
   *     example="Partial Title",
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *   @OA\Parameter(
   *     name="id_catalogs",
   *     in="query",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(
   *     name="id_priority",
   *     in="query",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddRequest $request): JsonResponse {
    $this->partialRepository->add($request->all());

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Catalog"},
   *   path="/api/partials/multi",
   *   summary="Multi Add a Partial Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="data",
   *     in="query",
   *     required=true,
   *     example="low[0]=Title Low 1&normal[0]=Title Normal 1&normal[1]=Title Normal 2&high[0]=Title High 1",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="season",
   *     in="query",
   *     required=true,
   *     example="Winter",
   *     @OA\Schema(type="string", enum={"Winter", "Spring", "Summer", "Fall"}),
   *   ),
   *   @OA\Parameter(
   *     name="year",
   *     in="query",
   *     required=true,
   *     example=2021,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       example={
   *         "status": 200,
   *         "message": "Success",
   *         "data": {
   *           "accepted": 0,
   *           "total": 0,
   *         },
   *       },
   *       @OA\Property(property="status", type="integer", format="int32"),
   *       @OA\Property(property="message", type="string"),
   *       @OA\Property(
   *         property="data",
   *         @OA\Property(property="accepted", type="integer", format="int32"),
   *         @OA\Property(property="total", type="integer", format="int32"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=400, ref="#/components/responses/BadRequest"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add_multiple(Request $request): JsonResponse {
    try {
      $data = [];
      parse_str($request->get('data'), $data);

      $total_count = 0;

      if (isset($data['low'])) $total_count += count($data['low']);
      if (isset($data['normal'])) $total_count += count($data['normal']);
      if (isset($data['high'])) $total_count += count($data['high']);

      $count = $this->partialRepository->add_multiple([
        'data' => $data,
        'season' => $request->get('season'),
        'year' => $request->get('year'),
      ]);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'accepted' => $count,
          'total' => $total_count,
        ],
      ]);
    } catch (TypeError) {
      return ErrorResponse::badRequest();
    }
  }

  /**
   * @OA\Put(
   *   tags={"Catalog"},
   *   path="/api/partials/{partial_id}",
   *   summary="Edit a Partial Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="partial_id",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(
   *     name="title",
   *     in="query",
   *     required=true,
   *     example="Partial Title",
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *   @OA\Parameter(
   *     name="id_catalogs",
   *     in="query",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(
   *     name="id_priority",
   *     in="query",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=400, ref="#/components/responses/BadRequest"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(EditRequest $request, $uuid): JsonResponse {
    try {
      $body = $request->only('title', 'id_catalogs', 'id_priority');
      $this->partialRepository->edit($body, $uuid);

      return DefaultResponse::success();
    } catch (TypeError) {
      return ErrorResponse::badRequest();
    }
  }

  /**
   * @OA\Put(
   *   tags={"Catalog"},
   *   path="/api/partials/multi/{catalog_id}",
   *   summary="Multi Add a Partial Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="catalog_id",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     description="Catalog ID",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(
   *     name="data",
   *     in="query",
   *     required=true,
   *     example="low[0]=Title Low 1&normal[0]=Title Normal 1&normal[1]=Title Normal 2&high[0]=Title High 1",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="season",
   *     in="query",
   *     required=true,
   *     example="Winter",
   *     @OA\Schema(type="string", enum={"Winter", "Spring", "Summer", "Fall"}),
   *   ),
   *   @OA\Parameter(
   *     name="year",
   *     in="query",
   *     required=true,
   *     example=2021,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       example={
   *         "status": 200,
   *         "message": "Success",
   *         "data": {
   *           "accepted": 0,
   *           "total": 0,
   *         },
   *       },
   *       @OA\Property(property="status", type="integer", format="int32"),
   *       @OA\Property(property="message", type="string"),
   *       @OA\Property(
   *         property="data",
   *         @OA\Property(property="accepted", type="integer", format="int32"),
   *         @OA\Property(property="total", type="integer", format="int32"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=400, ref="#/components/responses/BadRequest"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit_multiple(Request $request, $uuid): JsonResponse {
    try {
      $data = [];
      parse_str($request->get('data'), $data);

      $total_count = 0;

      if (isset($data['low'])) $total_count += count($data['low']);
      if (isset($data['normal'])) $total_count += count($data['normal']);
      if (isset($data['high'])) $total_count += count($data['high']);

      $count = $this->partialRepository->edit_multiple([
        'data' => $data,
        'season' => $request->get('season'),
        'year' => $request->get('year'),
      ], $uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'accepted' => $count,
          'total' => $total_count,
        ],
      ]);
    } catch (TypeError) {
      return ErrorResponse::badRequest();
    }
  }

  /**
   * @OA\Delete(
   *   tags={"Catalog"},
   *   path="/api/partials/{partial_id}",
   *   summary="Delete a Partial Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="partial_id",
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
    $this->partialRepository->delete($uuid);

    return DefaultResponse::success();
  }

  /* Temporarily removed as API are unused */

  // public function index($uuid) {
  //   return response()->json([
  //     'data' => $this->partialRepository->get($uuid),
  //   ]);
  // }
}
