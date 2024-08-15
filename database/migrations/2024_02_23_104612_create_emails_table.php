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
    Schema::create('emails', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('title');
      $table->string('template_id');
      $table->json('data')->default(new Expression('(JSON_OBJECT())'));
      $table->json('targets')->default(new Expression('(JSON_ARRAY())'));
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('emails');
  }

};
