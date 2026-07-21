<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", example: 1),
    new OA\Property(property: "date", type: "string", format: 'date-time', example: '2001-01-01T00:00:00.000000Z'),
    new OA\Property(property: "reading", type: "integer", example: 1),
  ]
)]
class ElectricityReading extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'electricity_readings';
  public $timestamps = null;

  protected $fillable = [
    'date',
    'reading',
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'date' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
