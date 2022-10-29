<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\CodecVideo;

class CodecVideoSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      ['codec' => 'x264 8bit'],
      ['codec' => 'x264 10bit'],
      ['codec' => 'x265 8bit'],
      ['codec' => 'x265 10bit'],
    ];


    foreach ($data as $item) {
      CodecVideo::create($item);
    }
  }
}
