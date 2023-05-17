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
   * @api {post} /api/auth/login User Login
   * @apiName UserLogin
   * @apiGroup User
   *
   * @apiBody {String} email Email
   * @apiBody {String} password Password
   *
   * @apiSuccess {Number} status API response code
   * @apiSuccess {String} message API response message
   * @apiSuccess {Object[]} data User Data
   * @apiSuccess {String} data.token Token to be used for API calls
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "status": 200,
   *       "message": "Success",
   *       "data": {
   *         "token": "<alphanumeric value>"
   *       }
   *     }
   *
   * @apiError Invalid Either the Username or Password is not provided
   * @apiError InvalidCredentials Either the Username or Password of the User is invalid
   *
   * @apiErrorExample Invalid
   *     HTTP/1.1 400 Bad Request
   *     {
   *       "status": 400,
   *       "message": "username and password fields are required"
   *     }
   *
   * @apiErrorExample InvalidCredentials
   *     HTTP/1.1 400 Bad Request
   *     {
   *       "status": 400,
   *       "message": "username or password is invalid"
   *     }
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
   * @api {post} /api/auth/logout User Logout
   * @apiName UserLogout
   * @apiGroup User
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Number} status API response code
   * @apiSuccess {String} message API response message
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "status": 200,
   *       "message": "Success"
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function logout(): JsonResponse {
    Auth::user()->tokens()->delete();

    return response()->json([
      'status' => 200,
      'message' => 'Success',
    ], 200);
  }

  /**
   * @api {get} /api/auth/user User Details
   * @apiName UserDetails
   * @apiGroup User
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Number} status API response code
   * @apiSuccess {String} message API response message
   * @apiSuccess {Object[]} data User Data
   * @apiSuccess {Number} data.id User Id
   * @apiSuccess {Number} data.email User Email
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "status": 200,
   *       "message": "Success"
   *       "data": [
   *         {
   *           id: 1,
   *           email: "test@mail.com",
   *         }
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function getUser(): JsonResponse {
    return response()->json(auth()->user());
  }
}
