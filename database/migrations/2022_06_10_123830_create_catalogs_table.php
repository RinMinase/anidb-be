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
    Schema::create('catalogs', function (Blueprint $table) {
      $table->id();

      $table->string('description', 16)->unique();
      $table->smallInteger('order')->unique()->nullable();

      $table->smallInteger('year')->nullable();
      $table->enum(
        'season',
        ['Winter', 'Spring', 'Summer', 'Fall'],
      )->nullable();

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
    Schema::dropIfExists('catalogs');
  }
};
