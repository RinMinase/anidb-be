<?php

namespace App\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

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

  #[OA\Post(
    path: "/api/auth/register",
    tags: ["User"],
    summary: "User Registration",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/user_register_username"),
      new OA\Parameter(ref: "#/components/parameters/user_register_password"),
      new OA\Parameter(ref: "#/components/parameters/user_register_password_confirmation"),
      new OA\Parameter(ref: "#/components/parameters/user_register_root_password"),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: "OK",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(property: "data", ref: "#/components/schemas/UserToken"),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
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

  #[OA\Post(
    path: "/api/auth/login",
    tags: ["User"],
    summary: "User Login",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/user_login_username"),
      new OA\Parameter(ref: "#/components/parameters/user_login_password"),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: "OK",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(property: "data", ref: "#/components/schemas/UserToken"),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/AuthInvalidCredentialsResponse"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
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

  #[OA\Post(
    path: "/api/auth/logout",
    tags: ["User"],
    summary: "User Logout",
    security: [["token" => [], "api-key" => []]],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function logout(): JsonResponse {
    $user = Auth::user();

    if ($user) {
      $user->tokens()->delete();
    } else {
      throw new AuthenticationException;
    }

    return DefaultResponse::success();
  }

  #[OA\Get(
    path: "/api/auth/user",
    tags: ["User"],
    summary: "Get User",
    security: [["token" => [], "api-key" => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: "OK",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(property: "data", ref: "#/components/schemas/UserDetails"),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getUser(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => auth()->user(),
    ]);
  }
}
