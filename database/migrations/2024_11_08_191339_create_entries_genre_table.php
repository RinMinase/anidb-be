<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('entries_genre', function (Blueprint $table) {
      $table->id();

      // parent entry
      $table->integer('id_entries')->unsigned()->nullable();
      $table->foreign('id_entries')->references('id')->on('entries')->onDelete('cascade');

      // child / offquel entry
      $table->integer('id_genres')->unsigned()->nullable();
      $table->foreign('id_genres')->references('id')->on('genres')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('entries_genre');
  }
};
