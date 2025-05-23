<?php

namespace App\Requests\Sequence;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="sequence_add_edit_title",
   *   name="title",
   *   in="query",
   *   required=true,
   *   example="Sample Sequence List",
   *   @OA\Schema(type="string"),
   * ),
   * @OA\Parameter(
   *   parameter="sequence_add_edit_date_from",
   *   name="date_from",
   *   in="query",
   *   required=true,
   *   example="2020-01-01",
   *   @OA\Schema(type="string", format="date"),
   * ),
   * @OA\Parameter(
   *   parameter="sequence_add_edit_date_to",
   *   name="date_to",
   *   in="query",
   *   required=true,
   *   example="2020-01-01",
   *   @OA\Schema(type="string", format="date"),
   * ),
   */
  public function rules() {
    return [
      'title' => ['required', 'string', 'max:128'],
      'date_from' => ['required', 'date'],
      'date_to' => ['required', 'date', 'after_or_equal:date_from'],
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
