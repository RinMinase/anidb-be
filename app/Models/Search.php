<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'id_user',
    'uuid',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'id',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'updated_at' => 'datetime:Y-m-d H:m:s',
    'created_at' => 'datetime:Y-m-d H:m:s',
  ];

  public function user() {
    return $this->belongsTo(User::class, 'id_user');
  }
}
