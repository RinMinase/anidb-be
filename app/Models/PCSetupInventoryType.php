<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int64", example=1),
 *   @OA\Property(property="inventoryType", type="string"),
 * )
 */
class PCSetupInventoryType extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'pc_setups_inventory_types';
  public $timestamps = null;

  protected $fillable = [
    'id',
    'inventory_type',
  ];

  protected $hidden = [];

  protected $casts = [];
}
