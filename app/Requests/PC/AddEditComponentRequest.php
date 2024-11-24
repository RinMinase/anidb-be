<?php

namespace App\Requests\PC;

use App\Rules\PositiveSignedIntRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditComponentRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_id_type",
   *   name="id_type",
   *   in="query",
   *   required=true,
   *   example=1,
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_name",
   *   name="name",
   *   in="query",
   *   required=true,
   *   @OA\Schema(type="string", minLength=1, maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_description",
   *   name="description",
   *   in="query",
   *   @OA\Schema(type="string", minLength=1, maxLength=64),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_price",
   *   name="price",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32", minimum=0),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_purchase_date",
   *   name="purchase_date",
   *   in="query",
   *   @OA\Schema(type="string", format="date"),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_purchase_location",
   *   name="purchase_location",
   *   in="query",
   *   @OA\Schema(type="string", minLength=1, maxLength=64),
   * ),
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_purchase_notes",
   *   name="purchase_notes",
   *   in="query",
   *   @OA\Schema(type="string", minLength=1, maxLength=64),
   * ),
   *
   * @OA\Parameter(
   *   parameter="pc_add_edit_component_is_onhand",
   *   name="is_onhand",
   *   in="query",
   *   @OA\Schema(type="boolean"),
   * ),
   */
  public function rules() {
    return [
      'id_type' => ['required', 'integer', 'exists:pc_component_types,id'],
      'title' => ['required', 'string', 'max:64'],
      'description' => ['nullable', 'string', 'max:64'],

      'price' => ['nullable', 'integer', 'min:0', new PositiveSignedIntRule],
      'purchase_date' => ['nullable', 'string', 'date'],
      'purchase_location' => ['nullable', 'string', 'max:64'],
      'purchase_notes' => ['nullable', 'string', 'max:64'],

      'is_onhand' => ['nullable', 'boolean'],
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
