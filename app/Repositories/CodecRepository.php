<?php

namespace App\Repositories;

use App\Models\CodecAudio;
use App\Models\CodecVideo;

class CodecRepository {

  public function getAll() {
    return [
      'audio' => $this->getAudio(),
      'video' => $this->getVideo(),
    ];
  }

  public function getAudio() {
    return CodecAudio::orderBy('order')
      ->orderBy('id')
      ->get();
  }

  public function addAudio(array $values) {
    return CodecAudio::create($values);
  }

  public function editAudio(array $values, $id) {
    return CodecAudio::where('id', $id)->update($values);
  }

  public function deleteAudio($id) {
    return CodecAudio::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function getVideo() {
    return CodecVideo::orderBy('order')
      ->orderBy('id')
      ->get();
  }

  public function addVideo(array $values) {
    return CodecVideo::create($values);
  }

  public function editVideo(array $values, $id) {
    return CodecVideo::where('id', $id)->update($values);
  }

  public function deleteVideo($id) {
    return CodecVideo::where('id', $id)
      ->firstOrFail()
      ->delete();
  }
}
