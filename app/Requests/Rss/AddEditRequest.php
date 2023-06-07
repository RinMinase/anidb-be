<?php

namespace App\Requests\Rss;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddEditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="rss_add_edit_title",
   *   name="title",
   *   in="query",
   *   required=true,
   *   example="Sample RSS Feed",
   *   @OA\Schema(type="string", minLength=1, maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="rss_add_edit_update_speed_mins",
   *   name="update_speed_mins",
   *   in="query",
   *   example=60,
   *   @OA\Schema(type="integer", format="int32", default=60),
   * ),
   * @OA\Parameter(
   *   parameter="rss_add_edit_url",
   *   name="url",
   *   in="query",
   *   required=true,
   *   example="https://example.com/",
   *   @OA\Schema(type="string", format="uri"),
   * ),
   * @OA\Parameter(
   *   parameter="rss_add_edit_max_items",
   *   name="max_items",
   *   in="query",
   *   example=250,
   *   @OA\Schema(type="integer", format="int32", default=250),
   * ),
   */
  public function rules() {
    return [
      'title' => 'required|string|max:64',
      'update_speed_mins' => [
        'integer',
        'min:15',
        'max:' . db_int_max('small'),
        function ($attribute, $value, $fail) {
          if ($value % 15 !== 0) {
            $fail($attribute . ' must be divisible by 15');
          }
        },
      ],
      'url' => 'required|string|url|max:512',
      'max_items' => 'integer|max:' . db_int_max('small'),
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
