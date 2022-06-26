<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bucket extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'from',
    'to',
    'size',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'created_at',
    'updated_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
  ];
}
