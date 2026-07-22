<?php

namespace App\Requests\Electricity;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class AddEditBillRequest extends FormRequest {

  #[OA\Parameter(
    parameter: "electricity_add_edit_bill_bill_date",
    name: "date",
    in: "query",
    required: true,
    example: "2001-01-01",
    schema: new OA\Schema(type: "string", format: 'date')
  )]
  #[OA\Parameter(
    parameter: "electricity_add_edit_bill_reading_total",
    name: "reading_total",
    in: "query",
    required: true,
    example: 1,
    schema: new OA\Schema(type: "integer")
  )]
  #[OA\Parameter(
    parameter: "electricity_add_edit_bill_total_cost",
    name: "total_cost",
    in: "query",
    required: true,
    example: 12.34,
    schema: new OA\Schema(type: "number", format: 'float')
  )]

  #[OA\Parameter(parameter: "electricity_add_edit_bill_period_from", name: "period_from", in: "query", example: "2001-01-01", schema: new OA\Schema(type: "string", format: 'date'))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_period_to", name: "period_to", in: "query", example: "2001-01-01", schema: new OA\Schema(type: "string", format: 'date'))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_avg_kwh", name: "avg_kwh", in: "query", example: 12.34, schema: new OA\Schema(type: "number", format: 'float'))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_reading_from", name: "reading_from", in: "query", example: 1, schema: new OA\Schema(type: "integer"))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_reading_to", name: "reading_to", in: "query", example: 1, schema: new OA\Schema(type: "integer"))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_price_generation", name: "price_generation", in: "query", example: 12.34, schema: new OA\Schema(type: "number", format: 'float'))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_price_distribution", name: "price_distribution", in: "query", example: 12.34, schema: new OA\Schema(type: "number", format: 'float'))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_price_govt", name: "price_govt", in: "query", example: 12.34, schema: new OA\Schema(type: "number", format: 'float'))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_kwh_price_generation_charge", name: "kwh_price_generation_charge", in: "query", example: 12.34, schema: new OA\Schema(type: "number", format: 'float'))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_kwh_price_distribution_charge", name: "kwh_price_distribution_charge", in: "query", example: 12.34, schema: new OA\Schema(type: "number", format: 'float'))]
  #[OA\Parameter(parameter: "electricity_add_edit_bill_notes", name: "notes", in: "query", example: "notes", schema: new OA\Schema(type: "string"))]
  public function rules() {
    return [
      'bill_date' => ['required', 'date', 'date_format:Y-m-d'],
      'period_from' => ['date', 'date_format:Y-m-d'],
      'period_to' => ['date', 'date_format:Y-m-d', 'after_or_equal:period_from'],

      'avg_kwh' => ['numeric', 'min:0'],
      'reading_from' => ['integer', 'min:0'],
      'reading_to' => ['integer', 'min:0', 'gte:reading_from'],

      'reading_total' => ['required', 'integer', 'min:0'],
      'total_cost' => ['required', 'numeric', 'min:0'],

      'price_generation' => ['numeric', 'min:0'],
      'price_distribution' => ['numeric', 'min:0'],
      'price_govt' => ['numeric', 'min:0'],

      'kwh_price_generation_charge' => ['numeric', 'min:0'],
      'kwh_price_distribution_charge' => ['numeric', 'min:0'],

      'notes' => ['string', 'max:255'],
    ];
  }
}
