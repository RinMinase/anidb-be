<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Models\Sequence;

class SequenceSeeder extends Seeder {

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $testData = [
      [
        'title' => 'Summer 2013',
        'date_from' => Carbon::parse('2013-03-24')->format('Y-m-d'),
        'date_to' => Carbon::parse('2013-06-09')->format('Y-m-d'),
      ],
      [
        'title' => 'Summer 2014',
        'date_from' => Carbon::parse('2014-04-01')->format('Y-m-d'),
        'date_to' => Carbon::parse('2014-06-15')->format('Y-m-d'),
      ],
      [
        'title' => 'Summer 2015',
        'date_from' => Carbon::parse('2015-03-29')->format('Y-m-d'),
        'date_to' => Carbon::parse('2015-06-08')->format('Y-m-d'),
      ],
    ];

    foreach ($testData as $item) {
      Sequence::create($item);
    }
  }
}
