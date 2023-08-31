<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $table = 'user_openai_chat';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if( Schema::hasTable($this->table) && !Schema::hasColumn($this->table, 'uuid' ) ) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->string('uuid')->nullable()->index()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if( Schema::hasTable($this->table) && Schema::hasColumn($this->table, 'uuid' ) ) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};
