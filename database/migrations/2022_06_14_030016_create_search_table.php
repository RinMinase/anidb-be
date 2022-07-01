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
    Schema::create('searches', function (Blueprint $table) {
      $table->id();

      $table->integer('id_user')->nullable();
      $table->foreign('id_user')->references('id')->on('users');

      $table->uuid('uuid');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('searches');
  }
};
