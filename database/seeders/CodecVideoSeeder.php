<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

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

    DB::table('codec_videos')->insert($data);
  }
}
