<?php

namespace App\Requests\PC;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetOwnersRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="pc_get_owners_show_hidden",
   *   name="show_hidden",
   *   in="query",
   *   example=true,
   *   @OA\Schema(type="boolean"),
   * ),
   */
  public function rules() {
    return [
      'show_hidden' => ['nullable', 'boolean'],
    ];
  }

  protected function prepareForValidation() {
    $this->merge([
      'show_hidden' => to_boolean($this->show_hidden),
    ]);
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
