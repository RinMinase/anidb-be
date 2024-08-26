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
      [
        'quality' => '4K 2160p',
        'vertical_pixels' => 2160,
      ],
      [
        'quality' => 'FHD 1080p',
        'vertical_pixels' => 1080,
      ],
      [
        'quality' => 'HD 720p',
        'vertical_pixels' => 720,
      ],
      [
        'quality' => 'HQ 480p',
        'vertical_pixels' => 480,
      ],
      [
        'quality' => 'LQ 360p',
        'vertical_pixels' => 360,
      ],
    ];

    DB::table('qualities')->insert($data);
  }
}
