<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Requests\Auth\LoginRequest;
use App\Requests\Auth\RegisterRequest;

use App\Models\User;

class AuthController extends Controller {

  /**
   * @OA\Post(
   *   tags={"User"},
   *   path="/api/auth/register",
   *   summary="User Registration",
   *   @OA\RequestBody(
   *     @OA\JsonContent(
   *       required={"email", "password", "password_confirmation"},
   *       example={
   *         "email": "user@mail.com",
   *         "password": "password",
   *         "password_confirmation": "password"
   *       },
   *       @OA\Property(property="email", type="string"),
   *       @OA\Property(property="password", type="string"),
   *       @OA\Property(property="password_confirmation", type="string"),
   *     )
   *   ),
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       example={
   *         "status": 200,
   *         "message": "Success",
   *         "data": {
   *           "token": "alphanumeric token"
   *         }
   *       },
   *       @OA\Property(property="status", type="number"),
   *       @OA\Property(property="message", type="string"),
   *       @OA\Property(
   *         property="data",
   *         type="object",
   *         @OA\Property(property="token", type="string"),
   *       ),
   *     )
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   *   @OA\Response(
   *     response=500,
   *     description="Failed",
   *     @OA\JsonContent(ref="#/components/schemas/Failed"),
   *   ),
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

    return response()->json([
      'status' => 200,
      'message' => 'Success',
      'data' => [
        'token' => $token,
      ],
    ], 200);
  }

  /**
   * @OA\Post(
   *   tags={"User"},
   *   path="/api/auth/login",
   *   summary="User Login",
   *   @OA\RequestBody(
   *     @OA\JsonContent(
   *       required={"email", "password"},
   *       example={
   *         "email": "user@mail.com",
   *         "password": "password",
   *       },
   *       @OA\Property(property="email", type="string"),
   *       @OA\Property(property="password", type="string"),
   *     )
   *   ),
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       example={
   *         "status": 200,
   *         "message": "Success",
   *         "data": {
   *           "token": "alphanumeric token"
   *         }
   *       },
   *       @OA\Property(property="status", type="number"),
   *       @OA\Property(property="message", type="string"),
   *       @OA\Property(
   *         property="data",
   *         type="object",
   *         @OA\Property(property="token", type="string"),
   *       ),
   *     )
   *   ),
   *   @OA\Response(
   *     response=400,
   *     description="Invalid Form or Invalid Credentials",
   *     @OA\JsonContent(
   *       examples={
   *         @OA\Examples(
   *           summary="Invalid Form",
   *           example="InvalidForm",
   *           value={
   *             "status": 400,
   *             "message": "username and password fields are required",
   *           },
   *         ),
   *         @OA\Examples(
   *           summary="Invalid Credentials",
   *           example="InvalidCredentials",
   *           value={
   *             "status": 400,
   *             "message": "username or password is invalid",
   *           },
   *         ),
   *       },
   *       @OA\Property(property="status", type="number"),
   *       @OA\Property(property="message", type="string"),
   *     )
   *   ),
   * )
   */
  public function login(LoginRequest $request): JsonResponse {
    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
      return response()->json([
        'status' => 401,
        'message' => 'Credentials does not match',
      ], 401);
    }

    $token = auth()
      ->user()
      ->createToken('API Token')
      ->plainTextToken;

    // token format "<id>|<alphanumeric>"
    $token = explode('|', $token)[1];

    return response()->json([
      'status' => 200,
      'message' => 'Success',
      'data' => [
        'token' => $token,
      ],
    ], 200);
  }

  /**
   * @OA\Post(
   *   tags={"User"},
   *   path="/api/auth/logout",
   *   summary="User Logout",
   *   security={{"bearerAuth":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(ref="#/components/schemas/Success"),
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   * )
   */
  public function logout(): JsonResponse {
    Auth::user()->tokens()->delete();

    return response()->json([
      'status' => 200,
      'message' => 'Success',
    ], 200);
  }

  /**
   * @OA\Get(
   *   tags={"User"},
   *   path="/api/auth/user",
   *   summary="Get User",
   *   security={{ "token": {} }},
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       example={
   *         "id": 1,
   *         "email": "test@mail.com",
   *       },
   *       @OA\Property(property="id", type="number"),
   *       @OA\Property(property="email", type="string"),
   *     )
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   * )
   */
  public function getUser(): JsonResponse {
    return response()->json(auth()->user());
  }
}
