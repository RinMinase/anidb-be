<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partial extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'title',
    'id_catalogs',
    'id_priority',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'id_catalogs',
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

  public function priority() {
    return $this->belongsTo(Catalog::class, 'id_priority');
  }
}
