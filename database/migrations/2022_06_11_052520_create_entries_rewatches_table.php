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
    Schema::create('entries_rewatch', function (Blueprint $table) {
      $table->id();

      $table->integer('id_entries')->unsigned();
      $table->foreign('id_entries')->references('id')->on('entries');

      $table->date('date_rewatched');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('entries_rewatch');
  }
};
