    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\AbsensiController;
    use App\Http\Controllers\QrController;
    use App\Http\Controllers\UserController;

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        // Absensi karyawan
        Route::post('/absen', [AbsensiController::class, 'absen']);
        Route::get('/history', [AbsensiController::class, 'history']);
        Route::get('/karyawan/dashboard', [AbsensiController::class, 'karyawanDashboard']);

        // Profil
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);

        // QR Code
        Route::post('/qr/generate', [QrController::class, 'generate']);
        Route::post('/absen/qr', [QrController::class, 'scanAbsen']);

        // Admin — Dashboard
        Route::get('/dashboard', [AbsensiController::class, 'dashboard']);

        // Admin — CRUD Absensi
        Route::post('/absensi', [AbsensiController::class, 'store']);
        Route::put('/absensi/{id}', [AbsensiController::class, 'update']);
        Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy']);

        // Admin — CRUD User
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });