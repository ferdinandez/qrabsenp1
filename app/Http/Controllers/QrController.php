<?php

namespace App\Http\Controllers;

use App\Models\QrToken;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrController extends Controller
{
    // Admin: generate QR token baru (berlaku 30 menit)
    public function generate(Request $req)
    {
        $user = $req->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Hapus token lama yang sudah expired
        QrToken::where('expired_at', '<', now())->delete();

        $token = Str::random(32);

        $qr = QrToken::create([
            'token'      => $token,
            'is_used'    => false,
            'expired_at' => now()->addMinutes(30),
        ]);

        return response()->json([
            'message'    => 'QR token berhasil dibuat',
            'token'      => $qr->token,
            'expired_at' => $qr->expired_at,
            'qr_url'     => url('/api/absen/qr?token=' . $qr->token),
        ]);
    }

    // Karyawan: scan QR → absen
    public function scanAbsen(Request $req)
    {
        $req->validate([
            'token'     => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $qr = QrToken::where('token', $req->token)->first();

        if (!$qr) {
            return response()->json(['message' => 'QR tidak valid'], 404);
        }

        if ($qr->is_used) {
            return response()->json(['message' => 'QR sudah digunakan'], 400);
        }

        if ($qr->expired_at->isPast()) {
            return response()->json(['message' => 'QR sudah kadaluarsa'], 400);
        }

        // Check today's attendance
        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $todayEnd = \Carbon\Carbon::now('Asia/Jakarta')->endOfDay();
        
        $user = $req->user();
        
        $absenMasuk = Absensi::where('user_id', $user->id)
            ->where('type', 'masuk')
            ->whereBetween('waktu', [$today, $todayEnd])
            ->first();
        
        $absenPulang = Absensi::where('user_id', $user->id)
            ->where('type', 'pulang')
            ->whereBetween('waktu', [$today, $todayEnd])
            ->first();

        // Determine attendance type
        if (!$absenMasuk) {
            $type = 'masuk';
        } elseif (!$absenPulang) {
            $type = 'pulang';
        } else {
            return response()->json(['message' => 'Anda sudah absen masuk dan pulang hari ini'], 400);
        }

        // Cek lokasi
        $kantor_lat = -6.8773288;
        $kantor_lon = 107.5758885;
        $jarak = $this->haversine($req->latitude, $req->longitude, $kantor_lat, $kantor_lon);

        if ($jarak > 0.05) {
            return response()->json(['message' => 'Diluar area kantor!'], 403);
        }

        // Tandai QR sudah dipakai (hanya untuk absen masuk)
        if ($type === 'masuk') {
            $qr->update(['is_used' => true]);
        }

        // Simpan absensi dengan server time Asia/Jakarta
        $waktuAbsen = \Carbon\Carbon::now('Asia/Jakarta');
        
        $absen = Absensi::create([
            'user_id'   => $user->id,
            'type'      => $type,
            'waktu'     => $waktuAbsen,
            'latitude'  => $req->latitude,
            'longitude' => $req->longitude,
        ]);

        return response()->json([
            'message' => 'Absen ' . $type . ' berhasil via QR',
            'type' => $type,
            'data'    => $absen,
            'server_time' => $waktuAbsen->toDateTimeString()
        ]);
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earth = 6371;
        $dLat  = deg2rad($lat2 - $lat1);
        $dLon  = deg2rad($lon2 - $lon1);
        $a     = sin($dLat / 2) ** 2 +
                 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
