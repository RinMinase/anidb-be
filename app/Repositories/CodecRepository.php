<?php

namespace App\Repositories;

use App\Models\CodecAudio;
use App\Models\CodecVideo;

class CodecRepository {

  public function getAll() {
    return [
      'audio' => CodecAudio::all(),
      'video' => CodecVideo::all(),
    ];
  }
}
