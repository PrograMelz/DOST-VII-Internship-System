<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('heading_text_color', 7)->nullable()->after('button_color');
            $table->string('body_text_color', 7)->nullable()->after('heading_text_color');
        });

        DB::table('system_settings')->update([
            'heading_text_color' => '#333333',
            'body_text_color' => '#333333',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['heading_text_color', 'body_text_color']);
        });
    }
};

