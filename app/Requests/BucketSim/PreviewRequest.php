<?php

namespace App\Requests\BucketSim;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\JsonRule;

class PreviewRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="bucket_sim_preview_buckets",
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
      'buckets' => ['required', 'string', new JsonRule],
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
