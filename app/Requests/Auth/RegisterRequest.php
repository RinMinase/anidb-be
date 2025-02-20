<?php

namespace App\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Parameter(
 *   parameter="user_register_username",
 *   name="username",
 *   in="query",
 *   required=true,
 *   example="username",
 *   @OA\Schema(type="string", minLength=4, maxLength=32),
 * ),
 * @OA\Parameter(
 *   parameter="user_register_password",
 *   name="password",
 *   in="query",
 *   required=true,
 *   example="password",
 *   @OA\Schema(type="string", minLength=4, maxLength=32),
 * ),
 * @OA\Parameter(
 *   parameter="user_register_password_confirmation",
 *   name="password_confirmation",
 *   in="query",
 *   required=true,
 *   example="password",
 *   @OA\Schema(type="string", minLength=4, maxLength=32),
 * ),
 * @OA\Parameter(
 *   parameter="user_register_root_password",
 *   name="root_password",
 *   in="query",
 *   required=true,
 *   example="root-password",
 *   @OA\Schema(type="string"),
 * ),
 */
class RegisterRequest extends FormRequest {

  public function rules() {
    return [
      'username' => ['required', 'string', 'min:4', 'max:32', 'unique:users,username'],
      'password' => ['required', 'string', 'min:4', 'max:32', 'confirmed'],
      'root_password' => ['required', 'string'],
    ];
  }

  public function failedValidation(Validator $validator) {
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ], 401)
    );
  }

  public function messages() {
    $validation = require config_path('validation.php');

    return array_merge($validation, []);
  }
}
