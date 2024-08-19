<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   example={
 *     "uuid": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "description": "Sample Buckets"
 *   },
 *   @OA\Property(property="uuid", type="string", format="uuid"),
 *   @OA\Property(property="description", type="string"),
 * )
 */
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

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];

  public function sims() {
    return $this->hasMany(BucketSim::class, 'id_sim_info');
  }
}
