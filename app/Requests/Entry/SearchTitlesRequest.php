<?php

namespace App\Requests\Entry;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchTitlesRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_search_titles_id",
   *   name="id",
   *   description="Entry ID, search should by default include this entry",
   *   in="query",
   *   example="87d66263-269c-4f7c-9fb8-dd78c4408ff6",
   *   @OA\Schema(type="string", format="uuid"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_search_titles_id_excluded",
   *   name="id_excluded",
   *   description="If true, id parameter is exluded instead of included",
   *   in="query",
   *   @OA\Schema(type="string", enum={"0", "1"}, default="0"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_search_titles_needle",
   *   name="needle",
   *   description="Search query; When blank is passed, get 10 alphanumerically arranged titles",
   *   in="query",
   *   example="title",
   *   @OA\Schema(type="string"),
   * ),
   */
  public function rules() {
    return [
      'id' => ['uuid', 'exists:entries,uuid'],
      'id_excluded' => ['nullable', 'boolean'],
      'needle' => ['string'],
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
