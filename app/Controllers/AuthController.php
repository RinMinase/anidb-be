<?php

namespace App\Controllers;

use App\Exceptions\Auth\InvalidCredentialsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Repositories\UserRepository;

use App\Requests\Auth\LoginRequest;
use App\Requests\Auth\AddEditRequest;

use App\Resources\DefaultResponse;

class AuthController extends Controller {

  private UserRepository $userRepository;

  public function __construct(UserRepository $userRepository) {
    $this->userRepository = $userRepository;
  }

  /**
   * @OA\Post(
   *   tags={"User"},
   *   path="/api/auth/register",
   *   summary="User Registration",
   *
   *   @OA\Parameter(
   *     name="username",
   *     in="query",
   *     required=true,
   *     example="username",
   *     @OA\Schema(type="string", minLength=4, maxLength=32),
   *   ),
   *   @OA\Parameter(
   *     name="password",
   *     in="query",
   *     required=true,
   *     example="password",
   *     @OA\Schema(type="string", minLength=4, maxLength=32),
   *   ),
   *   @OA\Parameter(
   *     name="password_confirmation",
   *     in="query",
   *     required=true,
   *     example="password",
   *     @OA\Schema(type="string", minLength=4, maxLength=32),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/UserToken"),
   *         ),
   *       }
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function register(AddEditRequest $request): JsonResponse {
    $user = $this->userRepository->add($request->only('username', 'password'));

    $token = $user->createToken('API Token')
      ->plainTextToken;

    // token format "<id>|<alphanumeric>"
    $token = explode('|', $token)[1];

    return DefaultResponse::success(null, [
      'data' => [
        'token' => $token,
      ]
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"User"},
   *   path="/api/auth/login",
   *   summary="User Login",
   *
   *   @OA\Parameter(
   *     name="username",
   *     in="query",
   *     required=true,
   *     example="username",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="password",
   *     in="query",
   *     required=true,
   *     example="password",
   *     @OA\Schema(type="string"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/UserToken"),
   *         ),
   *       }
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/AuthInvalidCredentialsResponse"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function login(LoginRequest $request): JsonResponse {
    $credentials = $request->only('username', 'password');

    if (!Auth::attempt($credentials)) {
      throw new InvalidCredentialsException();
    }

    $token = auth()
      ->user()
      ->createToken('API Token')
      ->plainTextToken;

    // token format "<id>|<alphanumeric>"
    $token = explode('|', $token)[1];

    return DefaultResponse::success(null, [
      'data' => [
        'token' => $token,
      ]
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"User"},
   *   path="/api/auth/logout",
   *   summary="User Logout",
   *   security={{"token":{}}},
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function logout(): JsonResponse {
    Auth::user()->tokens()->delete();

    return DefaultResponse::success();
  }

  /**
   * @OA\Get(
   *   tags={"User"},
   *   path="/api/auth/user",
   *   summary="Get User",
   *   security={{"token": {}}},
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/UserDetails"),
   *         ),
   *       }
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function getUser(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => auth()->user(),
    ]);
  }
}
