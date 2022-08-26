<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('bucket_sims', function (Blueprint $table) {
      $table->id();

      $table->integer('id_sim_info')->unsigned()->nullable();
      $table->foreign('id_sim_info')->references('id')->on('bucket_sim_infos');

      $table->char('from', 1);
      $table->char('to', 1);
      $table->bigInteger('size');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('bucket_sims');
  }
};
