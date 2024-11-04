<?php

namespace App\Controllers;

use TypeError;

use Illuminate\Http\JsonResponse;

use App\Repositories\PartialRepository;

use App\Requests\Partial\AddEditMultipleRequest;
use App\Requests\Partial\AddEditRequest;

use App\Resources\DefaultResponse;

use App\Exceptions\Partial\ParsingException;
use App\Requests\Partial\GetAllRequest;

class PartialController extends Controller {

  private PartialRepository $partialRepository;

  public function __construct(PartialRepository $partialRepository) {
    $this->partialRepository = $partialRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Catalog"},
   *   path="/api/partials",
   *   summary="Get All Partials in All Catalogs",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/partial_get_all_query"),
   *   @OA\Parameter(ref="#/components/parameters/partial_get_all_column"),
   *   @OA\Parameter(ref="#/components/parameters/partial_get_all_order"),
   *   @OA\Parameter(ref="#/components/parameters/partial_get_all_page"),
   *   @OA\Parameter(ref="#/components/parameters/partial_get_all_limit"),
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
   *             @OA\Items(ref="#/components/schemas/PartialWithCatalogResource"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(GetAllRequest $request) {
    $data = $this->partialRepository->getAll($request->only(
      'query',
      'column',
      'order',
      'page',
      'limit',
    ));

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'meta' => $data['meta'],
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Catalog"},
   *   path="/api/partials/{partial_id}",
   *   summary="Get Partial Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="partial_id",
   *     description="Partial ID",
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
   *             allOf={
   *               @OA\Schema(ref="#/components/schemas/PartialResource"),
   *               @OA\Schema(
   *                 @OA\Property(property="idPriority", type="integer", format="int32", example=1),
   *                 @OA\Property(
   *                   property="idCatalog",
   *                   type="string",
   *                   format="uuid",
   *                   example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *                 ),
   *               ),
   *             }
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($uuid) {
    return DefaultResponse::success(null, [
      'data' => $this->partialRepository->get($uuid),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Catalog"},
   *   path="/api/partials",
   *   summary="Add a Partial Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_id_catalog"),
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_id_priority"),
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_title"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->partialRepository->add(
      $request->only('id_catalog', 'id_priority', 'title')
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Catalog"},
   *   path="/api/partials/multi",
   *   summary="Multi Add a Partial Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_multiple_data"),
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_multiple_season"),
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_multiple_year"),
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
   *             @OA\Property(property="accepted", type="integer", format="int32", example=0),
   *             @OA\Property(property="total", type="integer", format="int32", example=0),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=400, ref="#/components/responses/PartialParsingResponse"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add_multiple(AddEditMultipleRequest $request): JsonResponse {
    try {
      $data = [];
      parse_str($request->get('data'), $data);

      if (!isset($data['low']) && !isset($data['normal']) && !isset($data['high'])) {
        throw new ParsingException();
      }

      $total_count = 0;

      if (isset($data['low'])) $total_count += count($data['low']);
      if (isset($data['normal'])) $total_count += count($data['normal']);
      if (isset($data['high'])) $total_count += count($data['high']);

      $count = $this->partialRepository->add_multiple([
        'data' => $data,
        'season' => $request->get('season'),
        'year' => $request->get('year'),
      ]);

      return DefaultResponse::success(null, [
        'data' => [
          'accepted' => $count,
          'total' => $total_count,
        ],
      ]);
    } catch (TypeError) {
      throw new ParsingException();
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
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_id_catalog"),
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_id_priority"),
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_title"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=400, ref="#/components/responses/BadRequest"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditRequest $request, $uuid): JsonResponse {
    $this->partialRepository->edit(
      $request->only('title', 'id_catalog', 'id_priority'),
      $uuid,
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Catalog"},
   *   path="/api/partials/multi/{catalog_id}",
   *   summary="Multi Edit a Partial Entry",
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
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_multiple_data"),
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_multiple_season"),
   *   @OA\Parameter(ref="#/components/parameters/partial_add_edit_multiple_year"),
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
   *             @OA\Property(property="accepted", type="integer", format="int32", example=0),
   *             @OA\Property(property="total", type="integer", format="int32", example=0),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=400, ref="#/components/responses/PartialParsingResponse"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit_multiple(AddEditMultipleRequest $request, $uuid): JsonResponse {
    try {
      $data = [];
      parse_str($request->get('data'), $data);

      if (!isset($data['low']) && !isset($data['normal']) && !isset($data['high'])) {
        throw new ParsingException();
      }

      $total_count = 0;

      if (isset($data['low'])) $total_count += count($data['low']);
      if (isset($data['normal'])) $total_count += count($data['normal']);
      if (isset($data['high'])) $total_count += count($data['high']);

      $count = $this->partialRepository->edit_multiple([
        'data' => $data,
        'season' => $request->get('season'),
        'year' => $request->get('year'),
      ], $uuid);

      return DefaultResponse::success(null, [
        'data' => [
          'accepted' => $count,
          'total' => $total_count,
        ],
      ]);
    } catch (TypeError) {
      throw new ParsingException();
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
}
