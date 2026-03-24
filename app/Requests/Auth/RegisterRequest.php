<?php

namespace App\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Parameter(
  parameter: "user_register_username",
  name: "username",
  in: "query",
  required: true,
  example: "username",
  schema: new OA\Schema(type: "string", minLength: 4, maxLength: 32),
)]
#[OA\Parameter(
  parameter: "user_register_password",
  name: "password",
  in: "query",
  required: true,
  example: "password",
  schema: new OA\Schema(type: "string", minLength: 4, maxLength: 32),
)]
#[OA\Parameter(
  parameter: "user_register_password_confirmation",
  name: "password_confirmation",
  in: "query",
  required: true,
  example: "password",
  schema: new OA\Schema(type: "string", minLength: 4, maxLength: 32),
)]
#[OA\Parameter(
  parameter: "user_register_root_password",
  name: "root_password",
  in: "query",
  required: true,
  example: "root-password",
  schema: new OA\Schema(type: "string"),
)]
class RegisterRequest extends FormRequest {

  public function rules() {
    return [
      'username' => ['required', 'string', 'min:4', 'max:32', 'alpha_num:ascii', 'unique:users,username'],
      'password' => ['required', 'string', 'min:4', 'max:32', 'alpha_num:ascii', 'confirmed'],
      'root_password' => ['required', 'string'],
    ];
  }
}
