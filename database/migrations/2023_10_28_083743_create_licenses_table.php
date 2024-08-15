<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

return new class extends Migration {

  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('licenses', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->string('name');
      $table->json('data')->default(new Expression('(JSON_OBJECT())'));
      $table->json('statuses')->default(new Expression('(JSON_ARRAY())'));
      $table->string('status');
      $table->string('checker_id')->nullable();
      $table->timestamp('checked_at')->nullable();
      $table->timestamps();

    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('licenses');
  }

};
