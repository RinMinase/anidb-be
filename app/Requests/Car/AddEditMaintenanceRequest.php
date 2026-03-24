<?php

namespace App\Requests\Car;

use Illuminate\Foundation\Http\FormRequest;

class AddEditMaintenanceRequest extends FormRequest {
  public function rules() {
    return [
      'date' => ['required', 'string', 'date', 'before_or_equal:today'],
      'description' => ['required', 'string'],
      'odometer' => ['nullable', 'integer', 'min:0'],
      'parts' => ['required', 'array', 'min:1'],
      'parts.*' => ['string', 'distinct', 'exists:car_maintenance_types,type'],
    ];
  }
}
