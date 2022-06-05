<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class AuthController extends Controller {

  public function register(Request $request): JsonResponse {
    $attr = $request->validate([
      'email' => 'required|string|email|unique:users,email',
      'password' => 'required|string|min:6|confirmed'
    ]);

    $user = User::create([
      'password' => bcrypt($attr['password']),
      'email' => $attr['email']
    ]);

    $token = $user->createToken('API Token')
      ->plainTextToken;

    // token format "<id>|<alphanumeric>"
    $token = explode('|', $token)[1];

    return response()->json([
      'status' => 'Success',
      'data' => [
        'token' => $token,
      ],
    ], 200);
  }

  public function login(Request $request): JsonResponse {
    $attr = $request->validate([
      'email' => 'required|string|email|',
      'password' => 'required|string|min:6'
    ]);

    if (!Auth::attempt($attr)) {
      return response()->json([
        'status' => 'Error',
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
      'status' => 'Success',
      'data' => [
        'token' => $token,
      ]
    ], 200);
  }

  public function logout(): JsonResponse {
    auth()->user()->tokens()->delete();

    return response()->json([
      'status' => 'Success',
    ], 200);
  }

  public function getUser(): JsonResponse {
    return response()->json(auth()->user());
  }
}
