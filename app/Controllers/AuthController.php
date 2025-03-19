<?php

namespace App\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Repositories\UserRepository;
use App\Requests\Auth\LoginRequest;
use App\Requests\Auth\RegisterRequest;
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
   *   @OA\Parameter(ref="#/components/parameters/user_register_username"),
   *   @OA\Parameter(ref="#/components/parameters/user_register_password"),
   *   @OA\Parameter(ref="#/components/parameters/user_register_password_confirmation"),
   *   @OA\Parameter(ref="#/components/parameters/user_register_root_password"),
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
  public function register(RegisterRequest $request): JsonResponse {
    $body = $request->only('username', 'password', 'root_password');

    // Root password is required to create admin accounts
    if (!config('app.registration_root_password')) {
      throw new AuthenticationException();
    }

    if (config('app.registration_root_password') !== $body['root_password']) {
      throw new AuthenticationException();
    }

    $user = $this->userRepository->add($body, true);

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
   *   @OA\Parameter(ref="#/components/parameters/user_login_username"),
   *   @OA\Parameter(ref="#/components/parameters/user_login_password"),
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
   *   security={{"token":{}, "api-key": {}}},
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function logout(): JsonResponse {
    $user = Auth::user();

    if ($user) {
      $user->tokens()->delete();
    } else {
      throw new AuthenticationException;
    }

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
