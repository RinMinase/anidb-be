<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\CodecAudio;

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

    foreach ($data as $item) {
      CodecAudio::create($item);
    }
  }
}
