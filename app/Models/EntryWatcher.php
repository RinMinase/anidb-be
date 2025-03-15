<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   example={
 *     "id": 1,
 *     "label": "label",
 *     "color": "#ffffff",
 *   },
 *   @OA\Property(property="id", type="integer", format="int32"),
 *   @OA\Property(property="label", type="string"),
 *   @OA\Property(property="color", type="string", description="Color hex code"),
 * )
 */
class EntryWatcher extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'entries_watchers';
  public $timestamps = null;

  protected $fillable = [
    'id',
    'label',
    'color',
  ];

  protected $hidden = [];
}
