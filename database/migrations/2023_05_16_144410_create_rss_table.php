<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('rss', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();

      $table->string('title', 64);
      $table->dateTime('last_updated_at')->nullable();
      $table->smallInteger('update_speed_mins')->default(60);
      $table->string('url', 512);
      $table->smallInteger('max_items')->default(250);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('rss');
  }
};
