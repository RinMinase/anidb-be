<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MarathonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $testData = [
        [
          'title' => 'Summer 2013',
          'date_from' => Carbon::parse('2013-03-24')->format('Y-m-d'),
          'date_to' => Carbon::parse('2013-06-09')->format('Y-m-d'),
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ],
        [
          'title' => 'Summer 2014',
          'date_from' => Carbon::parse('2014-04-01')->format('Y-m-d'),
          'date_to' => Carbon::parse('2014-06-15')->format('Y-m-d'),
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ],
        [
          'title' => 'Summer 2015',
          'date_from' => Carbon::parse('2015-03-29')->format('Y-m-d'),
          'date_to' => Carbon::parse('2015-06-08')->format('Y-m-d'),
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ],
      ];

      DB::table('marathons')->insert($testData);
    }
}
