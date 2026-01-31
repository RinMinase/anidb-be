<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\GenreRepository;

use App\Resources\DefaultResponse;

class GenreController extends Controller {

  private GenreRepository $genreRepository;

  public function __construct(GenreRepository $genreRepository) {
    $this->genreRepository = $genreRepository;
  }

  #[OA\Get(
    path: "/api/genres",
    tags: ["Dropdowns"],
    summary: "Get All Genres",
    security: [["token" => []], ["api-key" => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: "data",
                  type: "array",
                  items: new OA\Items(ref: "#/components/schemas/Genre")
                ),
              ]
            ),
          ]
        )
      ),
    ]
  )]
  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->genreRepository->getAll(),
    ]);
  }
}
