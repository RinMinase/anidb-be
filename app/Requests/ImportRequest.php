<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest {

  public function rules() {
    return [
      'file' => ['required', 'file', 'mimetypes:application/json'],
    ];
  }
}
