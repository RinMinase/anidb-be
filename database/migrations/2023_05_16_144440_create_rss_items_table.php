<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('rss_items', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();

      $table->integer('id_rss')->unsigned()->nullable();
      $table->foreign('id_rss')->references('id')->on('rss')->onDelete('cascade');

      $table->string('title', 256);
      $table->string('link', 256);
      $table->string('guid', 256)->nullable();
      $table->dateTime('date');

      $table->boolean('is_read')->default(false);
      $table->boolean('is_bookmarked')->default(false);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('rss_items');
  }
};
