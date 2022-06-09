<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class QualitySeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      ['quality' => '4K 2160p'],
      ['quality' => 'FHD 1080p'],
      ['quality' => 'HD 720p'],
      ['quality' => 'HQ 480p'],
      ['quality' => 'LQ 360p'],
    ];

    DB::table('qualities')->insert($data);
  }
}
