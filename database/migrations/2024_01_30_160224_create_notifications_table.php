<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('notifications', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->string('name');
      $table->string('title');
      $table->string('body');
      $table->string('image')->nullable();
      $table->json('data')->default(new Expression('(JSON_OBJECT())'));
      $table->enum('status', ['info', 'success', 'failed', 'warning'])->default('info');
      $table->json('targets_ids')->default(new Expression('(JSON_ARRAY())'));
      $table->json('hides_ids')->default(new Expression('(JSON_ARRAY())'));
      $table->json('seenes_ids')->default(new Expression('(JSON_ARRAY())'));
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('notifications');
  }

};
