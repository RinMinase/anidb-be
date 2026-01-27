<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('buckets', function (Blueprint $table) {
      $table->date('purchase_date')->nullable();
    });

    Schema::table('bucket_sims', function (Blueprint $table) {
      $table->date('purchase_date')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('buckets', function (Blueprint $table) {
      $table->dropColumn('purchase_date');
    });

    Schema::table('bucket_sims', function (Blueprint $table) {
      $table->dropColumn('purchase_date');
    });
  }
};
