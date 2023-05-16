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

      $table->dateTime('last_updated_at');
      $table->integer('update_speed_mins')->default(60);
      $table->string('url', 512);

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
