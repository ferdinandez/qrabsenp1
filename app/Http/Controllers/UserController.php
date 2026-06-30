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

        $users = User::orderBy('created_at', 'desc')->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'role'       => $u->role,
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
            'password' => 'required|min:6',
            'role'     => 'in:admin,karyawan',
        ]);

        $user = User::create([
            'name'     => $req->name,
            'email'    => $req->email,
            'password' => Hash::make($req->password),
            'role'     => $req->role ?? 'karyawan',
        ]);

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data'    => $user->only(['id', 'name', 'email', 'role']),
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
        ]);

        if ($req->filled('name'))     $user->name     = $req->name;
        if ($req->filled('email'))    $user->email    = $req->email;
        if ($req->filled('password')) $user->password = Hash::make($req->password);
        if ($req->filled('role'))     $user->role     = $req->role;

        $user->save();

        return response()->json([
            'message' => 'User berhasil diperbarui',
            'data'    => $user->only(['id', 'name', 'email', 'role']),
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
}
