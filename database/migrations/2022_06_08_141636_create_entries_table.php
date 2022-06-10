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

      $table->integer('id_quality')->nullable();
      $table->foreign('id_quality')->references('id')->on('qualities');

      $table->string('title', 256)->nullable();
      $table->date('date_finished')->nullable();
      $table->mediumInteger('duration')->nullable();
      $table->bigInteger('filesize')->nullable();

      $table->smallInteger('episodes')->default(0)->nullable();
      $table->smallInteger('ovas')->default(0)->nullable();
      $table->smallInteger('specials')->default(0)->nullable();

      $table->tinyInteger('season_number')->nullable();
      $table->integer('season_first_title')->nullable();
      $table->foreign('season_first_title')->references('id')->on('entries');

      $table->integer('prequel')->nullable();
      $table->foreign('prequel')->references('id')->on('entries');
      $table->integer('sequel')->nullable();
      $table->foreign('sequel')->references('id')->on('entries');

      $table->string('encoder_video', 128)->nullable();
      $table->string('encoder_audio', 128)->nullable();
      $table->string('encoder_subs', 128)->nullable();

      $table->smallInteger('release_year')->nullable();
      $table->enum(
        'release_season',
        ['Winter', 'Spring', 'Summer', 'Fall'],
      )->nullable();

      $table->string('variants', 256)->nullable();
      $table->string('remarks', 256)->nullable();

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
    Schema::dropIfExists('entries');
  }
};
