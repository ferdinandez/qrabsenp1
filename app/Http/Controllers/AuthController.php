<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absensi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $req->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $req->name,
            'email'    => $req->email,
            'password' => Hash::make($req->password),
            'role'     => $req->role ?? 'karyawan',
        ]);

        return response()->json($user, 201);
    }

    public function login(Request $req)
    {
        $user = User::where('email', $req->email)->first();

        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json(['message' => 'Login gagal'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    public function profile(Request $req)
    {
        $user = $req->user();

        $totalAbsensi = Absensi::where('user_id', $user->id)->count();
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('waktu', today())
            ->first();

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
            'statistik' => [
                'total_absensi'        => $totalAbsensi,
                'sudah_absen_hari_ini' => $absensiHariIni ? true : false,
                'waktu_absen_hari_ini' => $absensiHariIni?->waktu,
            ],
        ]);
    }

    public function updateProfile(Request $req)
    {
        $user = $req->user();

        $req->validate([
            'name'              => 'sometimes|string|max:255',
            'email'             => 'sometimes|email|unique:users,email,' . $user->id,
            'password'          => 'sometimes|min:6|confirmed',
        ]);

        if ($req->filled('name'))     $user->name  = $req->name;
        if ($req->filled('email'))    $user->email = $req->email;
        if ($req->filled('password')) $user->password = Hash::make($req->password);

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }
}
