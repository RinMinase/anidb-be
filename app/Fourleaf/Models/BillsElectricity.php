<?php

namespace App\Fourleaf\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="uuid",
 *     type="string",
 *     format="uuid",
 *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *   ),
 *   @OA\Property(
 *     property="kwh",
 *     type="integer",
 *     format="int16",
 *     example="123",
 *     minimum=0,
 *     maximum=32767,
 *   ),
 *   @OA\Property(
 *     property="cost",
 *     type="float",
 *     example=12.23,
 *   ),
 *   @OA\Property(
 *     property="date",
 *     type="string",
 *     example="Oct 2020",
 *   ),
 *   @OA\Property(
 *     property="costPerKwh",
 *     type="float",
 *     example=12.23,
 *   ),
 * )
 */
class BillsElectricity extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'fourleaf_bills_electricity';
  public $timestamps = null;

  protected $fillable = [
    'uuid',
    'uid',
    'kwh',
    'cost',
  ];

  protected $hidden = [
    'id'
  ];

  protected $casts = [];
}
