<?php

namespace App\Fourleaf\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddEditMaintenanceRequest extends FormRequest {
  public function rules() {
    return [
      'date' => ['required', 'string', 'date', 'before_or_equal:today'],
      'description' => ['required', 'string'],
      'parts.ac_coolant' => ['required', 'boolean'],
      'parts.battery' => ['required', 'boolean'],
      'parts.brake_fluid' => ['required', 'boolean'],
      'parts.engine_oil' => ['required', 'boolean'],
      'parts.power_steering_fluid' => ['required', 'boolean'],
      'parts.radiator_fluid' => ['required', 'boolean'],
      'parts.spark_plugs' => ['required', 'boolean'],
      'parts.tires' => ['required', 'boolean'],
      'parts.transmission' => ['required', 'boolean'],
      'parts.others' => ['required', 'boolean'],
      'odometer' => ['required', 'integer', 'min:0'],
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
