<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, number, boolean, json
            $table->string('category')->default('general'); // general, attendance, notification, system
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            // Work Hours
            [
                'key' => 'work_start_time',
                'value' => '08:00',
                'type' => 'string',
                'category' => 'attendance',
                'description' => 'Jam mulai kerja',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'work_end_time',
                'value' => '17:00',
                'type' => 'string',
                'category' => 'attendance',
                'description' => 'Jam selesai kerja',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_tolerance',
                'value' => '15',
                'type' => 'number',
                'category' => 'attendance',
                'description' => 'Toleransi keterlambatan (menit)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Geofencing
            [
                'key' => 'office_latitude',
                'value' => '-6.200000',
                'type' => 'string',
                'category' => 'attendance',
                'description' => 'Latitude kantor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'office_longitude',
                'value' => '106.816666',
                'type' => 'string',
                'category' => 'attendance',
                'description' => 'Longitude kantor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'geofence_radius',
                'value' => '100',
                'type' => 'number',
                'category' => 'attendance',
                'description' => 'Radius geofencing (meter)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // QR Settings
            [
                'key' => 'qr_expiry_minutes',
                'value' => '5',
                'type' => 'number',
                'category' => 'attendance',
                'description' => 'Durasi QR code aktif (menit)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // System
            [
                'key' => 'timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'category' => 'system',
                'description' => 'Timezone sistem',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_name',
                'value' => 'PT. Example Company',
                'type' => 'string',
                'category' => 'general',
                'description' => 'Nama perusahaan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Notifications
            [
                'key' => 'email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'notification',
                'description' => 'Aktifkan email notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'push_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'notification',
                'description' => 'Aktifkan push notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
