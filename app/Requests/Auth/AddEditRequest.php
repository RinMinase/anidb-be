<?php

namespace App\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

use App\Models\User;

class AddEditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="user_add_edit_username",
   *   name="username",
   *   in="query",
   *   required=true,
   *   example="username",
   *   @OA\Schema(type="string", minLength=4, maxLength=32),
   * ),
   * @OA\Parameter(
   *   parameter="user_add_edit_password",
   *   name="password",
   *   in="query",
   *   required=true,
   *   example="password",
   *   @OA\Schema(type="string", minLength=4, maxLength=32),
   * ),
   * @OA\Parameter(
   *   parameter="user_add_edit_password_confirmation",
   *   name="password_confirmation",
   *   in="query",
   *   required=true,
   *   example="password",
   *   @OA\Schema(type="string", minLength=4, maxLength=32),
   * ),
   */
  public function rules() {
    if ($this->route('uuid')) {
      $id = User::where('uuid', $this->route('uuid'))
        ->firstOrFail()
        ->id;
    }

    return [
      'username' => [
        'required',
        'string',
        'min:4',
        'max:32',
        'alpha_num:ascii',
        Rule::unique('users')->ignore($id ?? null)
      ],
      'password' => ['required', 'string', 'min:4', 'max:32', 'alpha_num:ascii', 'confirmed'],
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
