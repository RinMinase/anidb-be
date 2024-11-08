<?php

namespace App\Requests\Partial;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

use App\Enums\PartialOrderColumnsEnum;

class GetAllRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="partial_get_all_query",
   *   name="query",
   *   description="Search - Title to search for",
   *   in="query",
   *   example="",
   *   @OA\Schema(type="string"),
   * ),
   * @OA\Parameter(
   *   parameter="partial_get_all_column",
   *   name="column",
   *   description="Order - Column to order",
   *   in="query",
   *   example="id_catalog",
   *   @OA\Schema(type="string", default="id_catalog"),
   * ),
   * @OA\Parameter(
   *   parameter="partial_get_all_order",
   *   name="order",
   *   description="Order - Direction of order column",
   *   in="query",
   *   @OA\Schema(type="string", default="asc", enum={"asc", "desc"}),
   * ),
   * @OA\Parameter(
   *   parameter="partial_get_all_page",
   *   name="page",
   *   description="Pagination - Page to query",
   *   in="query",
   *   example=1,
   *   @OA\Schema(type="integer", format="int32", default=1, minimum=1),
   * ),
   * @OA\Parameter(
   *   parameter="partial_get_all_limit",
   *   name="limit",
   *   description="Pagination - Page item limit",
   *   in="query",
   *   example=30,
   *   @OA\Schema(type="integer", format="int32", default=30, minimum=1, maximum=9999),
   * ),
   */
  public function rules() {
    return [
      'query' => ['nullable', 'string'],
      'column' => [new Enum(PartialOrderColumnsEnum::class)],
      'order' => ['in:asc,desc,ASC,DESC'],
      'page' => ['integer', 'min:1'],
      'limit' => ['integer', 'min:1', 'max:9999'],
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