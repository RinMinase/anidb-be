<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [
      ['genre' => 'Action'],
      ['genre' => 'Adventure'],
      ['genre' => 'Comedy'],
      ['genre' => 'Drama'],
      ['genre' => 'Ecchi'],
      ['genre' => 'Fantasy'],
      ['genre' => 'Harem'],
      ['genre' => 'Horror'],
      ['genre' => 'Isekai'],
      ['genre' => 'Music'],
      ['genre' => 'Mystery'],
      ['genre' => 'Romance'],
      ['genre' => 'School'],
      ['genre' => 'Sci-Fi'],
      ['genre' => 'Slice of Life'],
      ['genre' => 'Sports'],
      ['genre' => 'Supernatural'],
      ['genre' => 'Suspense'],
    ];

    DB::table('genres')->insert($data);
  }
}
