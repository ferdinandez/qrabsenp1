<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing admin email or create new one
        $oldAdmin = User::where('email', 'admin@attendancex.com')->first();
        
        if ($oldAdmin) {
            // Update email jika admin lama ada
            $oldAdmin->update(['email' => 'attendx9@gmail.com']);
        } else {
            // Create new admin jika belum ada
            User::firstOrCreate(
                ['email' => 'attendx9@gmail.com'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('admin123'),
                    'role' => 'admin',
                    'status' => 'active',
                ]
            );
        }
    }

    public function down(): void
    {
        User::where('email', 'attendx9@gmail.com')
            ->update(['email' => 'admin@attendancex.com']);
    }
};
