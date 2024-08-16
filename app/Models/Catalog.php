<?php

namespace App\Models;

class Catalog extends BaseModel {

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
