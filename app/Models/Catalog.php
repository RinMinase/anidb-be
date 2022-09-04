<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'uuid',
    'year',
    'season',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'id',
    'updated_at',
    'deleted_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
  ];

  public function partials() {
    return $this->hasMany(Partial::class, 'id_catalogs');
  }

  public static function boot() {
    parent::boot();

    static::deleting(function ($catalog) {
      $catalog->partials()->delete();
    });
  }
}
