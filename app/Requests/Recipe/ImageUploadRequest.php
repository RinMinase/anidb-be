<?php

namespace App\Requests\Recipe;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Models\Recipe;

class ImageUploadRequest extends FormRequest {

  public function rules() {
    if ($this->route('id')) {
      Recipe::where('id', $this->route('id'))->firstOrFail();
    }

    return [
      'image' => [
        'required',
        'image',
        'mimes:jpeg,jpg,png',
        'max:4096',
      ],
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
