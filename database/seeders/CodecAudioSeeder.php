<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CodecAudioSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      ['codec' => 'AAC 2.0'],
      ['codec' => 'FLAC 2.0'],
      ['codec' => 'FLAC 5.1'],
      ['codec' => 'FLAC 7.1'],
      ['codec' => 'DTS-HD MA 2.0'],
      ['codec' => 'DTS-HD MA 5.1'],
      ['codec' => 'DTS-HD MA 7.1'],
      ['codec' => 'TrueHD 2.0'],
      ['codec' => 'TrueHD 5.1'],
      ['codec' => 'TrueHD 7.1'],
      ['codec' => 'TrueHD 5.1 Atmos'],
      ['codec' => 'TrueHD 7.1 Atmos'],
    ];

    DB::table('codec_audios')->insert($data);
  }
}
