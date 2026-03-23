<?php

namespace App\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest {

  public function rules() {
    return [
      'token' => ['required'],
      'email' => ['required', 'exists:password_reset_tokens,email'],
      'password' => ['required', 'string', 'min:4', 'max:32', 'alpha_num:ascii', 'confirmed'],
    ];
  }
}
