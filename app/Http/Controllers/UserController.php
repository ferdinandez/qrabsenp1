<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /api/users — list semua karyawan
    public function index(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Filter by status if provided
        $query = User::orderBy('created_at', 'desc');
        
        if ($req->filled('status')) {
            $query->where('status', $req->status);
        }

        $users = $query->get()->map(fn($u) => [
            'id'         => $u->id,
            'name'       => $u->name,
            'email'      => $u->email,
            'role'       => $u->role,
            'status'     => $u->status ?? 'active',
            'department' => $u->department,
            'position'   => $u->position,
            'phone'      => $u->phone,
            'avatar'     => $u->avatar ? url('storage/' . $u->avatar) : null,
            'created_at' => $u->created_at,
        ]);

        return response()->json([
            'total' => $users->count(),
            'data'  => $users,
        ]);
    }

    // GET /api/users/{id}
    public function show(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);

        return response()->json($user->only(['id', 'name', 'email', 'role', 'created_at']));
    }

    // POST /api/users — tambah karyawan baru
    public function store(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $req->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'nullable|min:6',
            'role'     => 'in:admin,karyawan',
            'status'   => 'in:pending,active,suspended',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'phone'    => 'nullable|string|max:20',
        ]);

        // Use default password if not provided
        $password = $req->password ?? 'password123';

        $user = User::create([
            'name'     => $req->name,
            'email'    => $req->email,
            'password' => Hash::make($password),
            'role'     => $req->role ?? 'karyawan',
            'status'   => $req->status ?? 'active',
            'department' => $req->department,
            'position' => $req->position,
            'phone'    => $req->phone,
        ]);

        // Notify the new user
        \App\Http\Controllers\NotificationController::create(
            $user->id,
            'user_created',
            'Selamat Datang di AttendX!',
            "Akun Anda telah dibuat oleh admin. Username: {$user->email}, Password default: {$password}. Silakan login dan ubah password Anda.",
            ['user_id' => $user->id]
        );
        
        // Email notification - send in background to avoid timeout
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->queue(new \App\Mail\WelcomeUserMail($user, $password));
        } catch (\Exception $e) {
            \Log::error('Failed to queue email: ' . $e->getMessage());
            // Don't fail the request if email fails
        }

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data'    => $user->only(['id', 'name', 'email', 'role', 'status', 'department', 'position', 'phone']),
            'default_password' => $password === 'password123' ? 'password123' : null,
        ], 201);
    }

    // PUT /api/users/{id} — edit karyawan
    public function update(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);

        $req->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|min:6',
            'role'     => 'sometimes|in:admin,karyawan',
            'status'   => 'sometimes|in:pending,active,suspended',
        ]);

        if ($req->filled('name'))       $user->name       = $req->name;
        if ($req->filled('email'))      $user->email      = $req->email;
        if ($req->filled('password'))   $user->password   = Hash::make($req->password);
        if ($req->filled('role'))       $user->role       = $req->role;
        if ($req->filled('status'))     $user->status     = $req->status;
        if ($req->filled('department')) $user->department = $req->department;
        if ($req->filled('position'))   $user->position   = $req->position;
        if ($req->filled('phone'))      $user->phone      = $req->phone;

        $user->save();

        return response()->json([
            'message' => 'User berhasil diperbarui',
            'data'    => $user->only(['id', 'name', 'email', 'role', 'status']),
        ]);
    }

    // DELETE /api/users/{id}
    public function destroy(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Cegah admin hapus dirinya sendiri
        if ($req->user()->id == $id) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri'], 400);
        }

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus']);
    }

    // POST /api/users/bulk-delete — hapus multiple users
    public function bulkDelete(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $req->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        // Cegah admin hapus dirinya sendiri
        $ids = array_filter($req->ids, fn($id) => $id != $req->user()->id);

        $deleted = User::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => "Berhasil menghapus {$deleted} user",
            'deleted' => $deleted,
        ]);
    }

    // PUT /api/users/{id}/change-password — ganti password (admin)
    public function changePassword(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);

        $req->validate([
            'password' => 'required|min:6',
        ]);

        $user->password = Hash::make($req->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah',
        ]);
    }

    // PUT /api/users/{id}/approve — approve pending user
    public function approve(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);

        $user->status = 'active';
        $user->save();

        return response()->json([
            'message' => 'User berhasil di-approve',
            'data'    => $user->only(['id', 'name', 'email', 'status']),
        ]);
    }

    // PUT /api/users/{id}/suspend — suspend user
    public function suspend(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);

        $user->status = 'suspended';
        $user->save();

        return response()->json([
            'message' => 'User berhasil di-suspend',
            'data'    => $user->only(['id', 'name', 'email', 'status']),
        ]);
    }

    // GET /api/users/pending — list pending users
    public function pending(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $pending = User::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'role'       => $u->role,
                'status'     => $u->status,
                'created_at' => $u->created_at,
            ]);

        return response()->json([
            'total' => $pending->count(),
            'data'  => $pending,
        ]);
    }
}
