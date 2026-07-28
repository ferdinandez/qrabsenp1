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
        Schema::table('absensis', function (Blueprint $table) {
            // Rename 'waktu' to 'check_in' for clarity
            $table->renameColumn('waktu', 'check_in');
        });
        
        Schema::table('absensis', function (Blueprint $table) {
            // Add new columns
            $table->timestamp('check_out')->nullable()->after('check_in');
            $table->decimal('work_hours', 5, 2)->nullable()->after('check_out'); // e.g., 8.50 hours
            $table->enum('status', ['on_time', 'late', 'early_leave', 'absent'])->default('on_time')->after('work_hours');
            $table->time('late_duration')->nullable()->after('status'); // How late (e.g., 00:15:00 = 15 minutes late)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['check_out', 'work_hours', 'status', 'late_duration']);
            $table->renameColumn('check_in', 'waktu');
        });
    }
};
