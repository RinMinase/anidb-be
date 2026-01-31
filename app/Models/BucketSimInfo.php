<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "uuid", type: "string", format: "uuid", example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158'),
    new OA\Property(property: "description", type: "string", example: 'Sample Buckets'),
  ]
)]
class BucketSimInfo extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'uuid',
    'description',
  ];

  protected $hidden = [
    'id',
    'created_at',
    'updated_at',
  ];

  protected function casts(): array {
    return [
      'created_at' => 'datetime:Y-m-d H:i:s',
    ];
  }

  public function sims() {
    return $this->hasMany(BucketSim::class, 'id_sim_info');
  }
}
