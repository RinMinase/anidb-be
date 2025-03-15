<?php

namespace App\Requests\Entry;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Models\Entry;

class OffquelsRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_offquels_data",
   *   name="data",
   *   in="query",
   *   required=true,
   *   example="data[0]=e9597119-8452-4f2b-96d8-f2b1b1d2f158&data[1]=786f90ce-87a6-4096-833b-a4a1db740f3d",
   *   @OA\Schema(type="string"),
   * ),
   */
  public function rules() {
    vdd($this->route('uuid'));

    if ($this->route('uuid')) {
      Entry::where('uuid', $this->route('uuid'))->firstOrFail();
    }

    return [
      'offquel_uuid' => ['required', 'string', 'uuid'],
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
