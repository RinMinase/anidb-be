<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "date", type: "string", example: "2020-10-20"),
    new OA\Property(property: "name", type: "string", example: "Example Appliance"),
  ]
)]
class ElectricityAppliance extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'electricity_appliances';
  public $timestamps = null;

  protected $fillable = [
    'date',
    'name',
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'date' => 'datetime:Y-m-d'
    ];
  }
}
