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
    Schema::create('partials', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid');

      $table->string('title', 256)->nullable();

      $table->integer('id_catalogs')->nullable();
      $table->foreign('id_catalogs')->references('id')->on('catalogs');

      $table->integer('id_priority')->nullable();
      $table->foreign('id_priority')->references('id')->on('priorities');

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
    Schema::dropIfExists('partials');
  }
};
