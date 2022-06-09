<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model {

  use SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'id_quality',
    'date_finished',
    'duration',
    'title',
    'filesize',
    'episodes',
    'ovas',
    'specials',
    'season_number',
    'season_first_title',
    'prequel',
    'sequel',
    'encoder_video',
    'encoder_audio',
    'encoder_subs',
    'release_year',
    'release_season',
    'variants',
    'remarks',
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
  protected $casts = [];

  public function rating() {
    return $this->hasOne(EntryRating::class);
  }

  public function offquels() {
    return $this->hasMany(EntryOffquel::class);
  }
}
