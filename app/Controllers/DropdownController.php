<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

use App\Repositories\CodecRepository;
use App\Repositories\EntryRepository;
use App\Repositories\GenreRepository;
use App\Repositories\GroupRepository;
use App\Repositories\QualityRepository;

use App\Resources\DefaultResponse;

class DropdownController extends Controller {

  private GroupRepository $groupRepository;
  private QualityRepository $qualityRepository;
  private CodecRepository $codecRepository;
  private GenreRepository $genreRepository;
  private EntryRepository $entryRepository;

  public function __construct(
    GroupRepository $groupRepository,
    QualityRepository $qualityRepository,
    CodecRepository $codecRepository,
    GenreRepository $genreRepository,
    EntryRepository $entryRepository,
  ) {
    $this->groupRepository = $groupRepository;
    $this->qualityRepository = $qualityRepository;
    $this->codecRepository = $codecRepository;
    $this->genreRepository = $genreRepository;
    $this->entryRepository = $entryRepository;
  }

  #[OA\Get(
    path: "/api/dropdowns",
    summary: "Get All Dropdowns for Adding Entries",
    security: [["token" => [], "api-key" => []]],
    tags: ["Dropdowns"],
    parameters: [
      new OA\Parameter(
        name: "query",
        description: "Comma-separated types of dropdowns to be requested",
        in: "query",
        example: "groups,qualities,codecs,genres,watchers",
        schema: new OA\Schema(type: "string")
      )
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(properties: [
              new OA\Property(property: "data", properties: [
                new OA\Property(property: "groups", type: "array", items: new OA\Items(type: "string")),
                new OA\Property(property: "qualities", type: "array", items: new OA\Items(ref: "#/components/schemas/Quality")),
                new OA\Property(property: "codecs", properties: [
                  new OA\Property(property: "audio", type: "array", items: new OA\Items(ref: "#/components/schemas/CodecAudio")),
                  new OA\Property(property: "video", type: "array", items: new OA\Items(ref: "#/components/schemas/CodecVideo")),
                ]),
                new OA\Property(property: "genres", type: "array", items: new OA\Items(ref: "#/components/schemas/Genre")),
                new OA\Property(property: "watchers", type: "array", items: new OA\Items(properties: [
                  new OA\Property(property: "id", type: "integer", format: "int32", example: 1),
                  new OA\Property(property: "label", type: "string", example: "label"),
                ])),
              ]),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function index(Request $request): JsonResponse {
    $query = $request->query('query');

    $query_split = explode(',', $query);

    foreach ($query_split as $key => $value) {
      $query_split[$key] = trim($value);
    }

    $query_valid_value = false;
    $data = [];

    if (in_array('groups', $query_split)) {
      $data['groups'] = $this->groupRepository->getNames();
      $query_valid_value = true;
    }

    if (in_array('qualities', $query_split)) {
      $data['qualities'] = $this->qualityRepository->getAll();
      $query_valid_value = true;
    }

    if (in_array('codecs', $query_split)) {
      $data['codecs'] = $this->codecRepository->getAll();
      $query_valid_value = true;
    }

    if (in_array('genres', $query_split)) {
      $data['genres'] = $this->genreRepository->getAll();
      $query_valid_value = true;
    }

    if (in_array('watchers', $query_split)) {
      $data['watchers'] = $this->entryRepository->get_watchers();
      $query_valid_value = true;
    }

    if (!$query_valid_value) {
      return DefaultResponse::success(null, [
        'data' => [
          'groups' => $this->groupRepository->getNames(),
          'qualities' => $this->qualityRepository->getAll(),
          'codecs' => $this->codecRepository->getAll(),
          'genres' => $this->genreRepository->getAll(),
          'watchers' => $this->entryRepository->get_watchers(),
        ],
      ]);
    }

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }
}
