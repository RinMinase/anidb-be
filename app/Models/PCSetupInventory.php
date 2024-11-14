<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int64", example=1),
 *   @OA\Property(property="inventoryType", type="string"),
 * )
 */
class PCSetupInventory extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $table = 'pc_setups_inventories';

  protected $fillable = [
    'uuid',
    'id_pc_setups_inventory_type',
    'name',
    'price',
    'purchase_date',
    'purchase_location',
    'is_onhand',
  ];

  protected $hidden = [
    'id'
  ];

  public function inventory_type() {
    return $this->belongsTo(PCSetupInventoryType::class, 'id_pc_setups_inventory_type');
  }

  protected $casts = [];
}
