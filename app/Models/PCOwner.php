<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="uuid",
 *     type="string",
 *     format="uuid",
 *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *   ),
 *   @OA\Property(property="name", type="string"),
 * )
 */
class PCOwner extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'pc_owners';
  public $timestamps = null;

  protected $fillable = [
    'uuid',
    'name',
  ];

  protected $hidden = [
    'id',
  ];

  protected $casts = [];
}
