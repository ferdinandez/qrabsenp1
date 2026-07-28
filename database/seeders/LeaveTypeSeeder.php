<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Cuti Tahunan',
                'code' => 'ANNUAL',
                'max_days_per_year' => 12,
                'requires_proof' => false,
                'color' => '#10B981',
                'description' => 'Cuti tahunan untuk karyawan tetap',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cuti Sakit',
                'code' => 'SICK',
                'max_days_per_year' => 10,
                'requires_proof' => true,
                'color' => '#EF4444',
                'description' => 'Cuti sakit dengan surat dokter',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin',
                'code' => 'PERMISSION',
                'max_days_per_year' => 6,
                'requires_proof' => false,
                'color' => '#F59E0B',
                'description' => 'Izin untuk keperluan mendadak',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cuti Khusus',
                'code' => 'SPECIAL',
                'max_days_per_year' => 5,
                'requires_proof' => true,
                'color' => '#8B5CF6',
                'description' => 'Cuti khusus (pernikahan, duka cita, dll)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('leave_types')->insert($leaveTypes);
    }
}
