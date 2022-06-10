<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      ['priority' => 'High'],
      ['priority' => 'Normal'],
      ['priority' => 'Low'],
    ];

    DB::table('priorities')->insert($data);
  }
}
