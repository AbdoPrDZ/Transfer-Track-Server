<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  /**
   * Run the migrations.
   */
  public function up(): void  {
    Schema::create('users', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->string('username');
      $table->string('email')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('phone_country_id');
      $table->string('phone');
      $table->string('password');
      $table->rememberToken();
      $table->string('messaging_token')->nullable();
      $table->enum('status', ['online', 'online_down', 'offline'])->default('offline');
      $table->json('setting')->default(new Expression("(JSON_OBJECT('theme_mode', 'dark', 'language', 'en', 'push_notifications', TRUE))"));
      $table->json("seenes_ids")->default(new Expression('(JSON_ARRAY())'));
      $table->string('license_id');
      $table->timestamps();
    });

    Schema::create('password_reset_tokens', function (Blueprint $table) {
      $table->string('email')->primary();
      $table->string('token');
      $table->timestamp('created_at')->nullable();
    });

    Schema::create('sessions', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->foreignId('user_id')->nullable()->index();
      $table->string('ip_address', 45)->nullable();
      $table->text('user_agent')->nullable();
      $table->longText('payload');
      $table->integer('last_activity')->index();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void  {
    Schema::dropIfExists('users');
    Schema::dropIfExists('password_reset_tokens');
    Schema::dropIfExists('sessions');
  }

};
