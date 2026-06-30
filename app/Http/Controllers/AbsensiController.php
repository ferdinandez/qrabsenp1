<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;

class AbsensiController extends Controller
{
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earth = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earth * $c;
    }

    public function absen(Request $req)
    {
        $user = $req->user();

        $lat = $req->latitude;
        $lon = $req->longitude;

        $kantor_lat = -6.8773288;
        $kantor_lon = 107.5758885;

        $jarak = $this->haversine($lat, $lon, $kantor_lat, $kantor_lon);

        if ($jarak > 1) {
            return response()->json([
                'message' => 'Diluar area!'
            ], 403);
        }

        $absen = Absensi::create([
            'user_id' => $user->id,
            'waktu' => now(),
            'latitude' => $lat,
            'longitude' => $lon
        ]);

        return response()->json([
            'message' => 'Absensi berhasil',
            'data' => $absen
        ]);
    }

    public function karyawanDashboard(Request $req)
    {
        $user = $req->user();

        $totalAbsensi = Absensi::where('user_id', $user->id)->count();

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('waktu', today())
            ->first();

        $absenBulanIni = Absensi::where('user_id', $user->id)
            ->whereMonth('waktu', now()->month)
            ->whereYear('waktu', now()->year)
            ->count();

        $history = Absensi::where('user_id', $user->id)
            ->orderBy('waktu', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
            'statistik' => [
                'total_absensi'        => $totalAbsensi,
                'absen_bulan_ini'      => $absenBulanIni,
                'sudah_absen_hari_ini' => $absensiHariIni ? true : false,
                'waktu_absen_hari_ini' => $absensiHariIni?->waktu,
            ],
            'history' => $history,
        ]);
    }

    public function history(Request $req)
    {
        $user = $req->user();

        // Admin: lihat semua history, Karyawan: hanya milik sendiri
        $query = Absensi::with('user:id,name,email')->orderBy('waktu', 'desc');

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $history = $query->get();

        return response()->json([
            'message' => 'History absensi',
            'total'   => $history->count(),
            'data'    => $history,
        ]);
    }

    public function dashboard(Request $req)
    {
        $user = $req->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $totalKaryawan = User::where('role', 'karyawan')->count();
        $totalAbsensiHariIni = Absensi::whereDate('waktu', today())->count();
        $totalAbsensiAll = Absensi::count();

        $absensiTerbaru = Absensi::with('user:id,name,email')
            ->orderBy('waktu', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'message' => 'Dashboard admin',
            'statistik' => [
                'total_karyawan'   => $totalKaryawan,
                'absensi_hari_ini' => $totalAbsensiHariIni,
                'total_absensi'    => $totalAbsensiAll,
            ],
            'absensi_terbaru' => $absensiTerbaru,
        ]);
    }

    // CRUD Absensi (admin only)

    // POST /api/absensi — tambah manual
    public function store(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $req->validate([
            'user_id'   => 'required|exists:users,id',
            'waktu'     => 'required|date',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $absen = Absensi::create([
            'user_id'   => $req->user_id,
            'waktu'     => $req->waktu,
            'latitude'  => $req->latitude,
            'longitude' => $req->longitude,
        ]);

        return response()->json([
            'message' => 'Absensi berhasil ditambahkan',
            'data'    => $absen->load('user:id,name,email'),
        ], 201);
    }

    // PUT /api/absensi/{id} — edit
    public function update(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $absen = Absensi::find($id);
        if (!$absen) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $req->validate([
            'user_id'   => 'sometimes|exists:users,id',
            'waktu'     => 'sometimes|date',
            'latitude'  => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
        ]);

        $absen->fill($req->only(['user_id', 'waktu', 'latitude', 'longitude']));
        $absen->save();

        return response()->json([
            'message' => 'Absensi berhasil diperbarui',
            'data'    => $absen->load('user:id,name,email'),
        ]);
    }

    // DELETE /api/absensi/{id}
    public function destroy(Request $req, $id)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $absen = Absensi::find($id);
        if (!$absen) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $absen->delete();

        return response()->json(['message' => 'Absensi berhasil dihapus']);
    }
}   

