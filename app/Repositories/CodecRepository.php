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

  public function getAudio() {
    return CodecAudio::all();
  }

  public function addAudio(string $codec) {
    return CodecAudio::create($codec);
  }

  public function editAudio(string $codec, $id) {
    return CodecAudio::where('id', $id)->update($codec);
  }

  public function deleteAudio($id) {
    return CodecAudio::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function getVideo() {
    return CodecVideo::all();
  }

  public function addVideo(string $codec) {
    return CodecVideo::create($codec);
  }

  public function editVideo(string $codec, $id) {
    return CodecVideo::where('id', $id)->update($codec);
  }

  public function deleteVideo($id) {
    return CodecVideo::where('id', $id)
      ->firstOrFail()
      ->delete();
  }
}
