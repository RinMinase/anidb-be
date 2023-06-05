<?php

namespace App\Requests\Log;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Enum;

use App\Enums\LogOrderColumns;

class SearchRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="log_search_column",
   *   name="column",
   *   description="Order - Column to order",
   *   in="query",
   *   example="created_at",
   *   @OA\Schema(type="string", default="created_at"),
   * ),
   * @OA\Parameter(
   *   parameter="log_search_order",
   *   name="order",
   *   description="Order - Direction of order column",
   *   in="query",
   *   @OA\Schema(type="string", default="desc", enum={"asc", "desc"}),
   * ),
   * @OA\Parameter(
   *   parameter="log_search_page",
   *   name="page",
   *   description="Pagination - Page to query",
   *   in="query",
   *   example=1,
   *   @OA\Schema(type="integer", format="int32", default=1, minimum=1),
   * ),
   * @OA\Parameter(
   *   parameter="log_search_limit",
   *   name="limit",
   *   description="Pagination - Page item limit",
   *   in="query",
   *   example=30,
   *   @OA\Schema(type="integer", format="int32", default=30, minimum=1, maximum=9999),
   * ),
   */
  public function rules() {
    return [
      'column' => [new Enum(LogOrderColumns::class)],
      'order' => 'in:asc,desc,ASC,DESC',
      'page' => 'integer|min:1',
      'limit' => 'integer|min:1|max:9999',
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
