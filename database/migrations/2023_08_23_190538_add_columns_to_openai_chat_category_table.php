<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('openai_chat_category', function (Blueprint $table) {
      $table->unsignedBigInteger('user_id')->default(0);
      $table->unsignedBigInteger('parent_id')->default(0);
      $table->string('type')->default('admin');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('openai_chat_category', function (Blueprint $table) {
      $table->dropColumn('user_id');
      $table->dropColumn('parent_id');
      $table->dropColumn('type');
    });
  }
};
