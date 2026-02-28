<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('recipes', function (Blueprint $table) {
      $table->id();

      $table->string('title');
      $table->string('description')->nullable();
    });

    // Manually add the native Postgres array column
    DB::statement('ALTER TABLE recipes ADD COLUMN ingredients text[]');

    Schema::table('recipes', function (Blueprint $table) {
      $table->longText('instructions')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('recipes');
  }
};
