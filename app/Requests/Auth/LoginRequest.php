<?php

namespace App\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use OpenApi\Attributes as OA;

#[OA\Parameter(
  parameter: "user_login_username",
  name: "username",
  in: "query",
  required: true,
  example: "username",
  schema: new OA\Schema(type: "string"),
)]
#[OA\Parameter(
  parameter: "user_login_password",
  name: "password",
  in: "query",
  required: true,
  example: "password",
  schema: new OA\Schema(type: "string"),
)]
class LoginRequest extends FormRequest {

  public function rules() {
    return [
      'username' => ['required', 'string'],
      'password' => ['required', 'string']
    ];
  }

  public function failedValidation(Validator $validator) {
    /** @disregard TypeInvalid */
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ], 401)
    );
  }
}
