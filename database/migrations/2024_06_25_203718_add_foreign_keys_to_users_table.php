<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('users', function (Blueprint $table) {
      $table->foreign('phone_country_id')
            ->references('code')
            ->on('phone_countries');
      $table->foreign('license_id')
            ->references('id')
            ->on('licenses');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('users', function (Blueprint $table) {
      $table->dropForeign(['phone_country_id']);
      $table->dropForeign(['license_id']);
    });
  }

};
