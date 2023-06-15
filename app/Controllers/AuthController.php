<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

use App\Requests\Auth\LoginRequest;
use App\Requests\Auth\RegisterRequest;

use App\Resources\DefaultResponse;
use App\Resources\ErrorResponse;

class AuthController extends Controller {

  /**
   * @OA\Post(
   *   tags={"User"},
   *   path="/api/auth/register",
   *   summary="User Registration",
   *
   *   @OA\Parameter(
   *     name="email",
   *     in="query",
   *     required=true,
   *     example="user@mail.com",
   *     @OA\Schema(type="string", format="email"),
   *   ),
   *   @OA\Parameter(
   *     name="password",
   *     in="query",
   *     required=true,
   *     example="password",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="password_confirmation",
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
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function register(RegisterRequest $request): JsonResponse {
    $user = User::create([
      'password' => bcrypt($request['password']),
      'email' => $request['email']
    ]);

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
   *     name="email",
   *     in="query",
   *     required=true,
   *     example="user@mail.com",
   *     @OA\Schema(type="string", format="email"),
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
   *   @OA\Response(
   *     response=401,
   *     description="Invalid Form or Invalid Credentials",
   *     @OA\JsonContent(
   *       examples={
   *         @OA\Examples(
   *           summary="Invalid Form",
   *           example="InvalidForm",
   *           value={
   *             "status": 401,
   *             "data": {
   *               "email": {"The email field is required."},
   *               "password": {"The password field is required."},
   *             },
   *           },
   *         ),
   *         @OA\Examples(
   *           summary="Invalid Credentials",
   *           example="InvalidCredentials",
   *           value={
   *             "status": 401,
   *             "message": "username or password is invalid",
   *           },
   *         ),
   *       },
   *       @OA\Property(
   *         property="data",
   *         description="Validation Errors",
   *         nullable=true,
   *         @OA\Property(property="email", type="array", @OA\Items(type="string")),
   *         @OA\Property(property="password", type="array", @OA\Items(type="string")),
   *       ),
   *     )
   *   ),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function login(LoginRequest $request): JsonResponse {
    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
      return ErrorResponse::unauthorized('Credentials does not match');
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
