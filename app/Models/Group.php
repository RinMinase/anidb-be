<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   example={
 *     "uuid": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "name": "Sample Group Name",
 *   },
 *   @OA\Property(property="uuid", type="string", format="uuid"),
 *   @OA\Property(property="name", type="string"),
 * )
 */
class Group extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'uuid',
    'name',
  ];

  protected $hidden = [
    'id',
    'created_at',
    'updated_at',
  ];

  protected $casts = [];
}
