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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('system_short_name', 20);
            $table->string('system_long_name', 150);
            $table->string('primary_color', 7);
            $table->string('secondary_color', 7)->nullable();
            $table->string('button_color', 7)->nullable();
            $table->string('system_logo', 255)->nullable();
            $table->dateTime('updated_at')->useCurrent();
        });

        DB::table('system_settings')->insert([
            'system_short_name' => 'IATS',
            'system_long_name' => 'Intern Attendance and Tracker System',
            'primary_color' => '#2E86C1',
            'secondary_color' => '#1B4F72',
            'button_color' => '#3498DB',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
