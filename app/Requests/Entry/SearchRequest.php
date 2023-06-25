<?php

namespace App\Requests\Entry;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

use App\Enums\EntryOrderColumnsEnum;
use App\Enums\EntrySearchColumnsEnum;

class SearchRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_search_needle",
   *   name="needle",
   *   description="Search - Item to search for in haystack (column)",
   *   in="query",
   *   example="item name",
   *   @OA\Schema(type="string"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_search_haystack",
   *   name="haystack",
   *   description="Search - Column to search for",
   *   in="query",
   *   example="title",
   *   @OA\Schema(type="string", default="title"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_search_column",
   *   name="column",
   *   description="Order - Column to order",
   *   in="query",
   *   example="id_quality",
   *   @OA\Schema(type="string", default="id_quality"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_search_order",
   *   name="order",
   *   description="Order - Direction of order column",
   *   in="query",
   *   @OA\Schema(type="string", default="asc", enum={"asc", "desc"}),
   * ),
   * @OA\Parameter(
   *   parameter="entry_search_page",
   *   name="page",
   *   description="Pagination - Page to query",
   *   in="query",
   *   example=1,
   *   @OA\Schema(type="integer", format="int32", default=1, minimum=1),
   * ),
   * @OA\Parameter(
   *   parameter="entry_search_limit",
   *   name="limit",
   *   description="Pagination - Page item limit",
   *   in="query",
   *   example=30,
   *   @OA\Schema(type="integer", format="int32", default=30, minimum=1, maximum=9999),
   * ),
   */
  public function rules() {
    return [
      'needle' => ['string'],
      'haystack' => [new Enum(EntrySearchColumnsEnum::class)],
      'column' => [new Enum(EntryOrderColumnsEnum::class)],
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
