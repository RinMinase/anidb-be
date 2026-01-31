<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "uuid", type: "string", format: "uuid", example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
    new OA\Property(property: "kwh", type: "integer", format: "int16", minimum: 0, maximum: 32767, example: 123),
    new OA\Property(property: "cost", type: "number", format: "float", example: 12.23),
    new OA\Property(property: "date", type: "string", example: "Oct 2020"),
    new OA\Property(property: "costPerKwh", type: "number", format: "float", example: 12.23),
  ]
)]
class BillsElectricity extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'fourleaf_bills_electricity';
  public $timestamps = null;

  protected $fillable = [
    'uuid',
    'uid',
    'kwh',
    'cost',
    'estimated_kwh',
  ];

  protected $hidden = [
    'id'
  ];
}
