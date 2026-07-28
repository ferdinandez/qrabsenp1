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

        // Check today's attendance
        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $todayEnd = \Carbon\Carbon::now('Asia/Jakarta')->endOfDay();
        
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

        // Office location configuration (dapat diubah di .env atau config)
        $officeLat = env('OFFICE_LATITUDE', -5.147665); // Default: contoh koordinat
        $officeLon = env('OFFICE_LONGITUDE', 119.432731);
        $maxRadius = env('OFFICE_RADIUS_KM', 0.5); // Default: 500 meter
        
        // Geofencing validation (set GEOFENCING_ENABLED=false di .env untuk disable)
        $geofencingEnabled = env('GEOFENCING_ENABLED', false);
        
        if ($geofencingEnabled) {
            $distance = $this->haversine($lat, $lon, $officeLat, $officeLon);
            
            if ($distance > $maxRadius) {
                return response()->json([
                    'message' => 'Anda berada di luar area kantor',
                    'distance' => round($distance, 2) . ' km',
                    'max_radius' => $maxRadius . ' km'
                ], 403);
            }
        }
        
        // Use server time with Asia/Jakarta timezone
        $waktuAbsen = \Carbon\Carbon::now('Asia/Jakarta');
        
        $absen = Absensi::create([
            'user_id' => $user->id,
            'type' => $type,
            'waktu' => $waktuAbsen,
            'latitude' => $lat,
            'longitude' => $lon
        ]);

        return response()->json([
            'message' => 'Absen ' . $type . ' berhasil',
            'type' => $type,
            'data' => $absen,
            'server_time' => $waktuAbsen->toDateTimeString()
        ]);
    }

    public function karyawanDashboard(Request $req)
    {
        $user = $req->user();

        $totalAbsensi = Absensi::where('user_id', $user->id)->count();

        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $todayEnd = \Carbon\Carbon::now('Asia/Jakarta')->endOfDay();
        
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereBetween('waktu', [$today, $todayEnd])
            ->first();

        $absenBulanIni = Absensi::where('user_id', $user->id)
            ->whereBetween('waktu', [
                \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth(),
                \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth()
            ])
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

        // Log for debugging
        \Log::info('History API called', [
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);

        // Admin: lihat semua history, Karyawan: hanya milik sendiri
        $query = Absensi::with('user:id,name,email')->orderBy('waktu', 'desc');

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        } else {
            // Admin can filter by specific user
            if ($req->filled('user_id')) {
                $query->where('user_id', $req->user_id);
            }
        }

        // Apply date filter if provided
        if ($req->filled('start_date')) {
            $query->whereDate('waktu', '>=', $req->start_date);
        }
        if ($req->filled('end_date')) {
            $query->whereDate('waktu', '<=', $req->end_date);
        }

        $history = $query->get();

        // Calculate statistics for the filtered user
        $statsUserId = ($user->role === 'admin' && $req->filled('user_id')) ? $req->user_id : $user->id;
        
        $totalAbsensi = Absensi::where('user_id', $statsUserId)->count();
        
        // Use Carbon with proper timezone
        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $todayEnd = \Carbon\Carbon::now('Asia/Jakarta')->endOfDay();
        
        // Debug: Get all absensis for this user with their dates
        $allAbsensis = Absensi::where('user_id', $statsUserId)
            ->get()
            ->map(function($a) use ($today) {
                $waktuCarbon = \Carbon\Carbon::parse($a->waktu)->timezone('Asia/Jakarta');
                return [
                    'id' => $a->id,
                    'waktu_raw' => $a->waktu,
                    'waktu_formatted' => $waktuCarbon->toDateTimeString(),
                    'date' => $waktuCarbon->toDateString(),
                    'is_today' => $waktuCarbon->isSameDay($today)
                ];
            });
        
        // Fix: Use whereBetween instead of whereDate for better timezone handling
        $hariIni = Absensi::where('user_id', $statsUserId)
            ->whereBetween('waktu', [$today, $todayEnd])
            ->count();
            
        $mingguIni = Absensi::where('user_id', $statsUserId)
            ->whereBetween('waktu', [
                \Carbon\Carbon::now('Asia/Jakarta')->startOfWeek(),
                \Carbon\Carbon::now('Asia/Jakarta')->endOfWeek()
            ])
            ->count();
            
        $bulanIni = Absensi::where('user_id', $statsUserId)
            ->whereBetween('waktu', [
                \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth(),
                \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth()
            ])
            ->count();

        $result = [
            'message' => 'History absensi',
            'total'   => $history->count(),
            'statistik' => [
                'total' => $totalAbsensi,
                'hari_ini' => $hariIni,
                'minggu_ini' => $mingguIni,
                'bulan_ini' => $bulanIni,
            ],
            'debug' => [
                'user_id' => $user->id,
                'today_start' => $today->toDateTimeString(),
                'today_end' => $todayEnd->toDateTimeString(),
                'start_of_week' => \Carbon\Carbon::now('Asia/Jakarta')->startOfWeek()->toDateTimeString(),
                'end_of_week' => \Carbon\Carbon::now('Asia/Jakarta')->endOfWeek()->toDateTimeString(),
                'start_of_month' => \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth()->toDateTimeString(),
                'end_of_month' => \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth()->toDateTimeString(),
                'month' => now()->month,
                'year' => now()->year,
                'all_absensis' => $allAbsensis,
                'timezone' => config('app.timezone'),
                'php_timezone' => date_default_timezone_get()
            ],
            'history' => $history,
        ];

        // Log the result
        \Log::info('History API result', [
            'statistik' => $result['statistik']
        ]);

        return response()->json($result);
    }

    public function dashboard(Request $req)
    {
        $user = $req->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $totalKaryawan = User::where('role', 'karyawan')->count();
        
        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $todayEnd = \Carbon\Carbon::now('Asia/Jakarta')->endOfDay();
        
        $totalAbsensiHariIni = Absensi::whereBetween('waktu', [$today, $todayEnd])->count();
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

    // GET /api/absensi — list all (admin)
    public function index(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $query = Absensi::with('user:id,name,email')->orderBy('waktu', 'desc');

        // Filter by date range
        if ($req->filled('start_date')) {
            $query->whereDate('waktu', '>=', $req->start_date);
        }
        if ($req->filled('end_date')) {
            $query->whereDate('waktu', '<=', $req->end_date);
        }

        // Filter by user
        if ($req->filled('user_id')) {
            $query->where('user_id', $req->user_id);
        }

        $absensi = $query->get();

        return response()->json([
            'message' => 'Data absensi',
            'total'   => $absensi->count(),
            'data'    => $absensi,
        ]);
    }

    // GET /api/statistics — weekly/monthly stats
    public function statistics(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $todayEnd = \Carbon\Carbon::now('Asia/Jakarta')->endOfDay();
        
        $todayCount = Absensi::whereBetween('waktu', [$today, $todayEnd])->count();
        
        $thisWeek = Absensi::whereBetween('waktu', [
            \Carbon\Carbon::now('Asia/Jakarta')->startOfWeek(),
            \Carbon\Carbon::now('Asia/Jakarta')->endOfWeek()
        ])->count();
        
        $thisMonth = Absensi::whereBetween('waktu', [
            \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth(),
            \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth()
        ])->count();
        
        $thisYear = Absensi::whereBetween('waktu', [
            \Carbon\Carbon::now('Asia/Jakarta')->startOfYear(),
            \Carbon\Carbon::now('Asia/Jakarta')->endOfYear()
        ])->count();

        // Top 10 karyawan dengan absensi terbanyak bulan ini
        $topKaryawan = Absensi::selectRaw('user_id, COUNT(*) as total')
            ->whereBetween('waktu', [
                \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth(),
                \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth()
            ])
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->with('user:id,name,email')
            ->get();

        // Weekly data (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now('Asia/Jakarta')->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            $count = Absensi::whereBetween('waktu', [$dayStart, $dayEnd])->count();
            $weeklyData[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('l'),
                'count' => $count
            ];
        }

        // Monthly data (last 4 weeks)
        $monthlyData = [];
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = \Carbon\Carbon::now('Asia/Jakarta')->subWeeks($i)->startOfWeek();
            $endOfWeek = \Carbon\Carbon::now('Asia/Jakarta')->subWeeks($i)->endOfWeek();
            $count = Absensi::whereBetween('waktu', [$startOfWeek, $endOfWeek])->count();
            $monthlyData[] = [
                'week' => 'Week ' . (4 - $i),
                'count' => $count
            ];
        }

        return response()->json([
            'message' => 'Statistik absensi',
            'statistik' => [
                'hari_ini'   => $todayCount,
                'minggu_ini' => $thisWeek,
                'bulan_ini'  => $thisMonth,
                'tahun_ini'  => $thisYear,
            ],
            'top_karyawan' => $topKaryawan,
            'weekly_data' => $weeklyData,
            'monthly_data' => $monthlyData
        ]);
    }

    // GET /api/report — summary report
    public function report(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $startDate = $req->input('start_date');
        $endDate = $req->input('end_date');

        // Get all users
        $users = User::where('role', 'karyawan')->get();

        $report = $users->map(function ($user) use ($startDate, $endDate) {
            $query = Absensi::where('user_id', $user->id);
            
            if ($startDate) {
                $query->whereDate('waktu', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('waktu', '<=', $endDate);
            }
            
            $totalAbsensi = $query->count();
            
            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'department' => $user->department,
                'position' => $user->position,
                'total_absensi' => $totalAbsensi,
                'status' => $user->status
            ];
        });

        return response()->json([
            'message' => 'Report summary',
            'report' => $report
        ]);
    }

    // GET /api/report/export — export CSV/PDF
    public function exportReport(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $query = Absensi::with('user:id,name,email')->orderBy('waktu', 'asc');

        // Filter by date range
        if ($req->filled('start_date')) {
            $query->whereDate('waktu', '>=', $req->start_date);
        }
        if ($req->filled('end_date')) {
            $query->whereDate('waktu', '<=', $req->end_date);
        }

        // Filter by user
        if ($req->filled('user_id')) {
            $query->where('user_id', $req->user_id);
        }

        $data = $query->get();
        
        // Check format parameter
        $format = $req->input('format', 'csv');
        
        if ($format === 'pdf') {
            return $this->exportPDF($data, $req);
        }

        // Default: CSV export
        $filename = 'laporan_absensi_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['ID', 'Nama', 'Email', 'Waktu', 'Latitude', 'Longitude']);

            // Data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->user->name ?? '-',
                    $row->user->email ?? '-',
                    $row->waktu,
                    $row->latitude,
                    $row->longitude,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    // Export to PDF
    private function exportPDF($data, $req)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.absensi', [
            'data' => $data,
            'start_date' => $req->input('start_date', '-'),
            'end_date' => $req->input('end_date', '-'),
            'generated_at' => now()->format('d/m/Y H:i:s')
        ]);
        
        $filename = 'laporan_absensi_' . date('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    // GET /api/karyawan/export — export personal (karyawan)
    public function exportPersonal(Request $req)
    {
        $user = $req->user();

        $query = Absensi::where('user_id', $user->id)->orderBy('waktu', 'asc');

        // Filter by date range (optional)
        if ($req->filled('start_date')) {
            $query->whereDate('waktu', '>=', $req->start_date);
        }
        if ($req->filled('end_date')) {
            $query->whereDate('waktu', '<=', $req->end_date);
        }

        $data = $query->get();
        
        // Check format parameter
        $format = $req->input('format', 'csv');
        
        if ($format === 'pdf') {
            return $this->exportPersonalPDF($data, $user, $req);
        }

        // Default: CSV export
        $filename = 'absensi_' . str_replace(' ', '_', $user->name) . '_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $user) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Nama', 'Email', 'Waktu', 'Latitude', 'Longitude']);

            // Data
            foreach ($data as $row) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $row->waktu,
                    $row->latitude,
                    $row->longitude,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    // Export personal to PDF
    private function exportPersonalPDF($data, $user, $req)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.personal-absensi', [
            'data' => $data,
            'user' => $user,
            'start_date' => $req->input('start_date', '-'),
            'end_date' => $req->input('end_date', '-'),
            'generated_at' => now()->format('d/m/Y H:i:s')
        ]);
        
        $filename = 'absensi_' . str_replace(' ', '_', $user->name) . '_' . date('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
