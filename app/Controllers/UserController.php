<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\UserRepository;
use App\Requests\Auth\AddEditRequest;
use App\Resources\DefaultResponse;

class UserController extends Controller {

  private UserRepository $userRepository;

  public function __construct(UserRepository $userRepository) {
    $this->userRepository = $userRepository;
  }

  /**
   * @OA\Get(
   *   tags={"User"},
   *   path="/api/users",
   *   summary="Get all non-admin users",
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
   *             @OA\Items(ref="#/components/schemas/UserDetails"),
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
      'data' => $this->userRepository->getAll(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"User"},
   *   path="/api/users/{user_uuid}",
   *   summary="Get a non-admin user",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="user_uuid",
   *     description="User UUID",
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
   *           @OA\Property(property="data", ref="#/components/schemas/UserDetails"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($uuid): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->userRepository->get($uuid),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"User"},
   *   path="/api/users",
   *   summary="Add a non-admin user",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/user_add_edit_username"),
   *   @OA\Parameter(ref="#/components/parameters/user_add_edit_password"),
   *   @OA\Parameter(ref="#/components/parameters/user_add_edit_password_confirmation"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->userRepository->add($request->only('username', 'password'));

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"User"},
   *   path="/api/users/{user_uuid}",
   *   summary="Edit a non-admin user",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="user_uuid",
   *     description="User UUID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(ref="#/components/parameters/user_add_edit_username"),
   *   @OA\Parameter(ref="#/components/parameters/user_add_edit_password"),
   *   @OA\Parameter(ref="#/components/parameters/user_add_edit_password_confirmation"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditRequest $request, $uuid): JsonResponse {
    $this->userRepository->edit($request->only('username', 'password'), $uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"User"},
   *   path="/api/users/{user_uuid}",
   *   summary="Delete a non-admin user",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="user_uuid",
   *     description="User UUID",
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
    $this->userRepository->delete($uuid);

    return DefaultResponse::success();
  }
}
