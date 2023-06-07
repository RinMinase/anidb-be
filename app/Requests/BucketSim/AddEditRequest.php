<?php

namespace App\Requests\BucketSim;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddEditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="bucket_sim_add_edit_description",
   *   name="description",
   *   description="Bucket Sim Description",
   *   in="query",
   *   required=true,
   *   example="Sample 2 buckets",
   *   @OA\Schema(type="string", minLength=1, maxLength=256),
   * ),
   * @OA\Parameter(
   *   parameter="bucket_sim_add_edit_buckets",
   *   name="buckets",
   *   description="Bucket JSON String",
   *   in="query",
   *   required=true,
   *   example="[{""from"":""a"",""to"":""i"",""size"":2000339066880},{""from"":""j"",""to"":""z"",""size"":2000339066880}]",
   *   @OA\Schema(type="string"),
   * ),
   */
  public function rules() {
    return [
      'description' => 'required|string|max:256',
      'buckets' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          if (!is_json($value)) {
            $fail($attribute . ' is not a valid JSON string');
          }
        },
      ],
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
