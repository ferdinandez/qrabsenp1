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
        // SKIP: Table absensis already created with timestamptz in create migration
        // This ALTER is only needed if migrating from existing MySQL/timestamp to PostgreSQL/timestamptz
        // For fresh PostgreSQL install, this is not needed
        
        // Check if column exists and is not already timestamptz
        if (Schema::hasColumn('absensis', 'waktu')) {
            $columnType = DB::select("SELECT data_type FROM information_schema.columns WHERE table_name = 'absensis' AND column_name = 'waktu'");
            if (!empty($columnType) && $columnType[0]->data_type !== 'timestamp with time zone') {
                DB::statement('ALTER TABLE absensis ALTER COLUMN waktu TYPE timestamptz USING waktu AT TIME ZONE \'UTC\'');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE absensis ALTER COLUMN waktu TYPE timestamp USING waktu::timestamp');
    }
};
