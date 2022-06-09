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
    Schema::create('entries', function (Blueprint $table) {
      $table->id();

      $table->date('date_finished')->nullable();
      // $table->string('download_priority')->nullable();
      $table->mediumInteger('duration')->unsigned()->nullable();
      $table->string('title', 256)->nullable();
      $table->bigInteger('filesize')->unsigned()->nullable();

      $table->smallInteger('episodes')->unsigned()->default(0)->nullable();
      $table->smallInteger('ovas')->unsigned()->default(0)->nullable();
      $table->smallInteger('specials')->unsigned()->default(0)->nullable();

      $table->tinyInteger('season_number')->unsigned()->nullable();
      $table->integer('season_first_title')->unsigned()->nullable();
      $table->foreign('season_first_title')->references('id')->on('entries');

      $table->integer('prequel')->unsigned()->nullable();
      $table->foreign('prequel')->references('id')->on('entries');
      $table->integer('sequel')->unsigned()->nullable();
      $table->foreign('sequel')->references('id')->on('entries');

      $table->string('encoder_video', 128)->nullable();
      $table->string('encoder_audio', 128)->nullable();
      $table->string('encoder_subs', 128)->nullable();

      $table->smallInteger('release_year')->unsigned()->nullable();
      $table->enum(
        'release_season',
        ['Winter', 'Spring', 'Summer', 'Fall'],
      )->nullable();

      $table->string('variants', 256)->nullable();
      $table->string('remarks', 256)->nullable();

      $table->timestamp('created_at');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('entries');
  }
};
