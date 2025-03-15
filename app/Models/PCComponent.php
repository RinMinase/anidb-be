<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(property="id", type="integer", format="int32", example=1),
 *   @OA\Property(property="idType", type="integer", format="int32", example=1),
 *   @OA\Property(property="name", type="string", example="Sample Component Name"),
 *   @OA\Property(property="description", type="string", example="Sample Component Description"),
 *   @OA\Property(property="price", type="integer", format="int32", example=10000),
 *   @OA\Property(property="priceEstimate", type="integer", format="int32", example=15000),
 *   @OA\Property(property="purchaseDate", type="string", example="2020-10-01"),
 *   @OA\Property(property="purchaseLocation", type="string", example="Store Name"),
 *   @OA\Property(property="purchaseNotes", type="string", example="Some notes"),
 *   @OA\Property(property="isOnhand", type="boolean", example=true),
 *   @OA\Property(property="isPurchased", type="boolean", example=true),
 *   @OA\Property(property="descriptiveName", type="string", example="Sample Component Name (10,000)", description="{name} ({price})"),
 * ),
 */
class PCComponent extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $table = 'pc_components';

  protected $fillable = [
    'id',
    'id_type',
    'name',
    'description',
    'price',
    'price_estimate',
    'purchase_date',
    'purchase_location',
    'purchase_notes',
    'is_onhand',
    'is_purchased',
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
      'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];
  }

  public function type() {
    return $this->belongsTo(PCComponentType::class, 'id_type');
  }
}
