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
    Schema::create('entries_rating', function (Blueprint $table) {
      $table->id();

      $table->integer('id_entries')->unsigned()->nullable();
      $table->foreign('id_entries')->references('id')->on('entries');

      $table->tinyInteger('audio')->unsigned()->default(0)->nullable();
      $table->tinyInteger('enjoyment')->unsigned()->default(0)->nullable();
      $table->tinyInteger('graphics')->unsigned()->default(0)->nullable();
      $table->tinyInteger('plot')->unsigned()->default(0)->nullable();

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('entries_rating');
  }
};
