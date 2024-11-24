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
 *   @OA\Property(property="idOwner", type="integer", format="int32", example=1),
 *   @OA\Property(property="label", type="string", example="Upcoming Setup"),
 *   @OA\Property(property="isActive", type="boolean", example=false),
 *   @OA\Property(property="isHidden", type="boolean", example=false),
 * )
 */
class PCInfo extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $table = 'pc_infos';
  public $timestamps = null;

  protected $fillable = [
    'uuid',
    'id_owner',
    'label',
    'is_active',
    'is_hidden',
  ];

  protected $hidden = [
    'id',
  ];

  protected $casts = [];

  public function owner() {
    return $this->belongsTo(PCOwner::class, 'id_owner');
  }
}
