<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model {

  protected $fillable = [
    'id_user',
    'uuid',
  ];

  protected $hidden = [
    'id',
  ];

  protected $casts = [
    'updated_at' => 'datetime:Y-m-d H:i:s',
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];

  public function user() {
    return $this->belongsTo(User::class, 'id_user');
  }
}
