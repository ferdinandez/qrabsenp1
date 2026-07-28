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

        // Jika register dari public (bukan admin), role = karyawan & status = pending
        $role = $req->role ?? 'karyawan';
        $status = ($role === 'karyawan') ? 'pending' : 'active';

        $user = User::create([
            'name'     => $req->name,
            'email'    => $req->email,
            'password' => Hash::make($req->password),
            'role'     => $role,
            'status'   => $status,
        ]);

        $message = ($status === 'pending') 
            ? 'Registrasi berhasil! Akun Anda menunggu approval admin.' 
            : 'Registrasi berhasil!';

        return response()->json([
            'message' => $message,
            'user'    => $user->only(['id', 'name', 'email', 'role', 'status']),
        ], 201);
    }

    public function login(Request $req)
    {
        $user = User::where('email', $req->email)->first();

        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json(['message' => 'Login gagal'], 401);
        }

        // Cek status akun
        if ($user->status === 'pending') {
            return response()->json(['message' => 'Akun Anda masih menunggu approval admin'], 403);
        }

        if ($user->status === 'suspended') {
            return response()->json(['message' => 'Akun Anda telah di-suspend'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role,
                'status' => $user->status,
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
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'phone'  => $user->phone,
                'role'   => $user->role,
                'avatar' => $user->avatar ? url('storage/' . $user->avatar) : null,
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
            'phone'             => 'sometimes|string|max:20',
            'password'          => 'sometimes|min:6|confirmed',
        ]);

        if ($req->filled('name'))     $user->name  = $req->name;
        if ($req->filled('email'))    $user->email = $req->email;
        if ($req->filled('phone'))    $user->phone = $req->phone;
        if ($req->filled('password')) $user->password = Hash::make($req->password);

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role'  => $user->role,
            ],
        ]);
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    public function changePassword(Request $req)
    {
        $user = $req->user();

        $req->validate([
            'current_password'      => 'required',
            'password'              => 'required|min:6|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($req->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($req->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }

    public function uploadAvatar(Request $req)
    {
        \Log::info('=== UPLOAD AVATAR START ===');
        \Log::info('User ID: ' . ($req->user() ? $req->user()->id : 'NULL'));
        \Log::info('Has file: ' . ($req->hasFile('avatar') ? 'YES' : 'NO'));
        \Log::info('Request files: ' . json_encode($req->allFiles()));
        \Log::info('Headers: ' . json_encode($req->headers->all()));
        
        $user = $req->user();

        if (!$user) {
            \Log::error('User not authenticated');
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $req->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        try {
            // Delete old avatar if exists
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Log::info('Deleting old avatar: ' . $user->avatar);
                \Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            \Log::info('Storing new avatar...');
            $path = $req->file('avatar')->store('avatars', 'public');
            \Log::info('Avatar stored at: ' . $path);

            // Update user avatar
            $user->avatar = $path;
            $user->save();
            
            $avatarUrl = url('storage/' . $path);
            \Log::info('Avatar URL: ' . $avatarUrl);
            \Log::info('=== UPLOAD AVATAR SUCCESS ===');

            return response()->json([
                'message' => 'Foto profil berhasil diupload',
                'avatar_url' => $avatarUrl,
            ]);
        } catch (\Exception $e) {
            \Log::error('=== UPLOAD AVATAR ERROR ===');
            \Log::error('Exception: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'message' => 'Gagal upload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAvatar(Request $req)
    {
        $user = $req->user();

        if (!$user->avatar) {
            return response()->json([
                'message' => 'Tidak ada foto profil untuk dihapus'
            ], 400);
        }

        try {
            // Delete avatar file
            if (\Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }

            // Remove avatar from database
            $user->avatar = null;
            $user->save();

            return response()->json([
                'message' => 'Foto profil berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
        }
    }
}
