<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GroupSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    $data = [];
    $groups = [
      'A&C',
      'Anbu',
      'ANE',
      'AonE',
      'Asakura',
      'Asenshi',
      'Beatrice-Raws',
      'BlurayDesuYo',
      'Chihiro',
      'Coalgirls',
      'Commie',
      'Cyan',
      'DameDesuYo',
      'DeadNews',
      'DmonHiro',
      'Doki',
      'Erai-raws',
      'EMBER',
      'GSK_kun',
      'HorribleSubs',
      'iAHD',
      'IrizaRaws',
      'Kametsu',
      'kBaraka',
      'LostYears',
      'LowPower-Raws',
      'Moozzi2',
      'MTBB',
      'neko-raws',
      'Nep_Blanc',
      'Nii-sama',
      'Project-GXS',
      'Rasetsu',
      'Raws-Maji',
      'Reinforce',
      'sam',
      'SallySubs',
      'sergey_krs',
      'SCP-2223',
      'ShowY',
      'Snow-Raws',
      'SubsPlease',
      'THORA',
      'tlacatlc6',
      'Tsundere',
      'UCCUSS',
      'VCB-Studio',
      'Vivid',
      'Yousei-raws',
      'Zurako',
    ];

    foreach ($groups as $group) {
      array_push($data, [
        'name' => $group,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }

    DB::table('groups')->insert($data);
  }
}
