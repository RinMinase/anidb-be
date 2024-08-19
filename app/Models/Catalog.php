<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

class Catalog extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'uuid',
    'year',
    'season',
  ];

  protected $hidden = [
    'id',
    'updated_at',
    'deleted_at',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];

  public function partials() {
    return $this->hasMany(Partial::class, 'id_catalog');
  }

  public static function boot() {
    parent::boot();

    static::deleting(function ($catalog) {
      $catalog->partials()->delete();
    });
  }
}
