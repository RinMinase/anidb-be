<?php

namespace App\Requests\Entry;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Entry;

class ImageUploadRequest extends FormRequest {

  public function rules() {
    if ($this->route('uuid')) {
      Entry::where('uuid', $this->route('uuid'))->firstOrFail();
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
}
