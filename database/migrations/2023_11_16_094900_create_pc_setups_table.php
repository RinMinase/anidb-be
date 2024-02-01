<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('pc_setups', function (Blueprint $table) {
      $table->id();

      $table->string('label', 64);

      $table->boolean('is_current')->default(false)->nullable();
      $table->boolean('is_future')->default(false)->nullable();
      $table->boolean('is_server')->default(false)->nullable();

      $table->string('cpu', 64)->nullable();
      $table->integer('cpu_price')->unsigned()->nullable();
      $table->string('cpu_sub', 64)->nullable();
      $table->string('cpu_sub2', 64)->nullable();

      $table->string('ram', 64)->nullable();
      $table->integer('ram_price')->unsigned()->nullable();
      $table->string('ram_sub', 64)->nullable();

      $table->string('gpu', 64)->nullable();
      $table->integer('gpu_price')->unsigned()->nullable();
      $table->string('gpu_sub', 64)->nullable();

      $table->string('motherboard', 64)->nullable();
      $table->integer('motherboard_price')->unsigned()->nullable();

      $table->string('psu', 64)->nullable();
      $table->integer('psu_price')->unsigned()->nullable();

      $table->string('cooler', 64)->nullable();
      $table->integer('cooler_price')->unsigned()->nullable();
      $table->string('cooler_acc', 64)->nullable();
      $table->integer('cooler_acc_price')->unsigned()->nullable();

      $table->string('ssd_1', 64)->nullable();
      $table->integer('ssd_1_price')->unsigned()->nullable();
      $table->string('ssd_2', 64)->nullable();
      $table->integer('ssd_2_price')->unsigned()->nullable();
      $table->string('ssd_3', 64)->nullable();
      $table->integer('ssd_3_price')->unsigned()->nullable();
      $table->string('ssd_4', 64)->nullable();
      $table->integer('ssd_4_price')->unsigned()->nullable();

      $table->string('hdd_1', 64)->nullable();
      $table->integer('hdd_1_price')->unsigned()->nullable();
      $table->string('hdd_2', 64)->nullable();
      $table->integer('hdd_2_price')->unsigned()->nullable();
      $table->string('hdd_3', 64)->nullable();
      $table->integer('hdd_3_price')->unsigned()->nullable();
      $table->string('hdd_4', 64)->nullable();
      $table->integer('hdd_4_price')->unsigned()->nullable();

      $table->string('case', 64)->nullable();
      $table->integer('case_price')->unsigned()->nullable();

      $table->string('case_fans_1', 64)->nullable();
      $table->integer('case_fans_1_price')->unsigned()->nullable();
      $table->string('case_fans_2', 64)->nullable();
      $table->integer('case_fans_2_price')->unsigned()->nullable();
      $table->string('case_fans_3', 64)->nullable();
      $table->integer('case_fans_3_price')->unsigned()->nullable();
      $table->string('case_fans_4', 64)->nullable();
      $table->integer('case_fans_4_price')->unsigned()->nullable();

      $table->string('monitor', 64)->nullable();
      $table->integer('monitor_price')->unsigned()->nullable();
      $table->string('monitor_sub', 64)->nullable();

      $table->string('monitor_acc_1', 64)->nullable();
      $table->integer('monitor_acc_1_price')->unsigned()->nullable();

      $table->string('monitor_acc_2', 64)->nullable();
      $table->integer('monitor_acc_2_price')->unsigned()->nullable();

      $table->string('keyboard', 64)->nullable();
      $table->integer('keyboard_price')->unsigned()->nullable();
      $table->string('keyboard_sub', 64)->nullable();
      $table->string('keyboard_sub2', 64)->nullable();

      $table->string('keyboard_acc_1', 64)->nullable();
      $table->integer('keyboard_acc_1_price')->unsigned()->nullable();
      $table->string('keyboard_acc_2', 64)->nullable();
      $table->integer('keyboard_acc_2_price')->unsigned()->nullable();

      $table->string('mouse', 64)->nullable();
      $table->integer('mouse_price')->unsigned()->nullable();

      $table->string('speakers', 64)->nullable();
      $table->integer('speakers_price')->unsigned()->nullable();

      $table->string('wifi', 64)->nullable();
      $table->integer('wifi_price')->unsigned()->nullable();

      $table->string('headset_1', 64)->nullable();
      $table->integer('headset_1_price')->unsigned()->nullable();
      $table->string('headset_2', 64)->nullable();
      $table->integer('headset_2_price')->unsigned()->nullable();

      $table->string('mic', 64)->nullable();
      $table->integer('mic_price')->unsigned()->nullable();
      $table->string('mic_acc', 64)->nullable();
      $table->integer('mic_acc_price')->unsigned()->nullable();

      $table->string('audio_interface', 64)->nullable();
      $table->integer('audio_interface_price')->unsigned()->nullable();

      $table->string('equalizer', 64)->nullable();
      $table->integer('equalizer_price')->unsigned()->nullable();

      $table->string('amplifier', 64)->nullable();
      $table->integer('amplifier_price')->unsigned()->nullable();

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('pc_setups');
  }
};
