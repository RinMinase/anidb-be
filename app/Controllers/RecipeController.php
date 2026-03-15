<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

use App\Repositories\RecipeRepository;
use App\Requests\Recipe\AddEditRequest;
use App\Resources\DefaultResponse;

class RecipeController extends Controller {

  private RecipeRepository $recipeRepository;

  public function __construct(RecipeRepository $recipeRepository) {
    $this->recipeRepository = $recipeRepository;
  }


  #[OA\Get(
    tags: ['Recipes'],
    path: '/api/recipes',
    summary: 'Get All Recipes',
    security: [['token' => [], 'api-key' => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: 'data',
                  type: 'array',
                  items: new OA\Items(ref: '#/components/schemas/Recipe')
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->recipeRepository->get_all(),
    ]);
  }

  #[OA\Get(
    tags: ['Recipes'],
    path: '/api/recipes/{recipe_id}',
    summary: 'Get Recipe',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'recipe_id',
        description: 'Recipe ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/Recipe'),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function get($id): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->recipeRepository->get($id),
    ]);
  }

  #[OA\Post(
    tags: ['Recipes'],
    path: '/api/recipes',
    summary: 'Add a Recipe',
    security: [['token' => [], 'api-key' => []]],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(ref: "#/components/schemas/RecipeAddEditRequest")
    ),
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add(AddEditRequest $request): JsonResponse {
    $this->recipeRepository->add(
      $request->only('title', 'description', 'ingredients', 'instructions'),
    );

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['Recipes'],
    path: '/api/recipes/{recipe_id}',
    summary: 'Edit a Recipe',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'recipe_id',
        description: 'Recipe ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(ref: "#/components/schemas/RecipeAddEditRequest")
    ),
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit(AddEditRequest $request, $id): JsonResponse {
    $this->recipeRepository->edit(
      $request->only('title', 'description', 'ingredients', 'instructions'),
      $id,
    );

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['Recipes'],
    path: '/api/recipes/{recipe_id}',
    summary: 'Delete a Recipe',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'recipe_id',
        description: 'Recipe ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function delete($id): JsonResponse {
    $this->recipeRepository->delete($id);

    return DefaultResponse::success();
  }

  #[OA\Post(
    tags: ['Recipes'],
    path: '/api/recipes/img-upload/{recipe_id}',
    summary: 'Upload an Image to Recipe',
    description: "POST request with '_method' in parameters, because PHP can't populate files in PUT/PATCH requests :: Ref. https://stackoverflow.com/a/65009135",
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'recipe_id',
        description: 'Recipe ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\MediaType(
        mediaType: 'multipart/form-data',
        schema: new OA\Schema(properties: [
          new OA\Property(property: '_method', type: 'string', example: 'PUT'),
          new OA\Property(property: 'image', type: 'string', format: 'binary'),
        ])
      )
    ),
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function imageUpload(Request $request, $id): JsonResponse {
    $request->validate(['image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:4096']]);

    $this->recipeRepository->uploadImage($request->file('image')->getRealPath(), $id);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['Recipes'],
    path: '/api/entries/img-upload/{recipe_id}',
    summary: 'Delete an Image of a Recipe',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'recipe_id',
        description: 'Recipe ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function imageDelete($id): JsonResponse {
    $this->recipeRepository->deleteImage($id);

    return DefaultResponse::success();
  }
}
