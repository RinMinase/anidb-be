<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('exports', function (Blueprint $table) {
      $table->uuid('id')->default(DB::raw('(gen_random_uuid())'))->primary();

      $table->enum('type', ['json', 'sql', 'xlsx'])->default('json');
      $table->boolean('is_finished');
      $table->boolean('is_automated');

      $table->timestamp('created_at');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('exports');
  }
};
