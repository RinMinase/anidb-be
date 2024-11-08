<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\GenreRepository;

use App\Resources\DefaultResponse;

class GenreController extends Controller {

  private GenreRepository $genreRepository;

  public function __construct(GenreRepository $genreRepository) {
    $this->genreRepository = $genreRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Dropdowns"},
   *   path="/api/genres",
   *   summary="Get All Genres",
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
   *             @OA\Items(ref="#/components/schemas/Genre"),
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
      'data' => $this->genreRepository->getAll(),
    ]);
  }
}
