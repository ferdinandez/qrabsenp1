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
        // Create admin user if not exists
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

    public function down(): void
    {
        User::where('email', 'attendx9@gmail.com')->delete();
    }
};
