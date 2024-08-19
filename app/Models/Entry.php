<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\RefreshableAutoIncrements;

class Entry extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $fillable = [
    'uuid',
    'id_quality',
    'title',
    'date_finished',
    'duration',
    'filesize',
    'episodes',
    'ovas',
    'specials',
    'encoder_video',
    'encoder_audio',
    'encoder_subs',
    'release_year',
    'release_season',
    'variants',
    'remarks',
    'image',
    'id_codec_audio',
    'id_codec_video',
    'codec_hdr',
  ];

  protected $hidden = [
    'id',
    'id_quality',
    'updated_at',
    'deleted_at',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];

  public function quality() {
    return $this->belongsTo(Quality::class, 'id_quality');
  }

  public function season_first_title() {
    return $this->belongsTo(self::class, 'season_first_title_id');
  }

  public function prequel() {
    return $this->belongsTo(self::class, 'prequel_id');
  }

  public function sequel() {
    return $this->belongsTo(self::class, 'sequel_id');
  }

  public function rating() {
    return $this->hasOne(EntryRating::class, 'id_entries');
  }

  public function offquels() {
    return $this->hasMany(EntryOffquel::class, 'id_entries');
  }

  public function rewatches() {
    return $this->hasMany(EntryRewatch::class, 'id_entries');
  }

  public function codec_video() {
    return $this->belongsTo(CodecVideo::class, 'id_codec_video');
  }

  public function codec_audio() {
    return $this->belongsTo(CodecAudio::class, 'id_codec_audio');
  }
}
