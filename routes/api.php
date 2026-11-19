<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfilApiController;
use App\Http\Controllers\Api\PelatihApiController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\TarianApiController;
use App\Http\Controllers\Api\GaleriApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\GeminiController;
use App\Http\Controllers\Api\TopengApiController;
use App\Http\Controllers\Api\ArchiveApiController;
use App\Http\Controllers\Api\GuestApiController;
use App\Http\Controllers\Api\UjianApiController;
use App\Http\Controllers\AttendanceController;
use App\Models\JadwalLatihan;
use App\Models\PendaftaranTari;
use App\Models\Kehadiran;

Route::prefix('v1')->group(function () {

    // ── PUBLIC ────────────────────────────────────────────────
    Route::get('/profil',      [ProfilApiController::class,  'index']);
    Route::get('/pelatih',     [PelatihApiController::class, 'index']);
    Route::get('/events',      [EventApiController::class,   'index']);
    Route::get('/events/{id}', [EventApiController::class,   'show']);
    Route::get('/tarian',      [TarianApiController::class,  'index']);
    Route::get('/tarian/{id}', [TarianApiController::class,  'show']);
    Route::get('/galeri',      [GaleriApiController::class,  'index']);
    Route::post('/ai/chat', [GeminiController::class, 'chat'])->middleware('throttle:20,1');
    Route::post('/ai/recommend', [GeminiController::class, 'recommendDance'])->middleware('throttle:10,1');
    Route::get('/archive', [ArchiveApiController::class, 'index']);
    Route::get('/topeng', [TopengApiController::class, 'index']);
    Route::get('/topeng/{id}', [TopengApiController::class, 'show']);
    Route::post('/tamu', [GuestApiController::class, 'store']);

    Route::get('/jadwal', function () {
        $data = JadwalLatihan::where('aktif', true)->orderBy('urutan')->get();
        return response()->json(['data' => $data]);
    });

    Route::get('/file/{path}', [\App\Http\Controllers\PublicFileController::class, 'show'])->where('path', '.*');

    // ── AUTH ──────────────────────────────────────────────────
    Route::post('/auth/login',    [AuthApiController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/auth/register', [AuthApiController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/auth/verify-otp', [AuthApiController::class, 'verifyOtp'])->middleware('throttle:5,1');
    Route::post('/auth/resend-otp', [AuthApiController::class, 'resendOtp'])->middleware('throttle:1,1');
    Route::post('/auth/google',   [AuthApiController::class, 'loginWithGoogle']); // Google Sign-In (Firebase)

    // ── PROTECTED ─────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/auth/me',      [AuthApiController::class, 'me']);
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::put('/auth/profile', [AuthApiController::class, 'updateProfile']);
        Route::post('/auth/foto', [AuthApiController::class, 'updateFoto']);
        Route::put('/auth/password', [AuthApiController::class, 'updatePassword']);
        
        Route::post('/auth/update-fcm-token', function (Illuminate\Http\Request $req) {
            $req->validate(['fcm_token' => 'required|string']);
            $req->user()->update(['fcm_token' => $req->fcm_token]);
            return response()->json(['success' => true, 'message' => 'FCM Token updated.']);
        });

        Route::get('/rapor-saya', function (Illuminate\Http\Request $req) {
            $data = \App\Models\RaporPagelaran::with(['event', 'tarian', 'pelatih'])
                ->where('user_id', $req->user()->id)
                ->join('events', 'rapor_pagelaran.event_id', '=', 'events.id')
                ->orderBy('events.tanggal', 'desc')
                ->select('rapor_pagelaran.*')
                ->get();
            return response()->json(['data' => $data]);
        });

        Route::get('/rapor-saya/summary', function (Illuminate\Http\Request $req) {
            $userId = $req->user()->id;
            $totalPagelaran = \App\Models\RaporPagelaran::where('user_id', $userId)->count();
            
            if ($totalPagelaran === 0) {
                return response()->json(['data' => null]);
            }
            
            $summary = \App\Models\RaporPagelaran::where('user_id', $userId)
                ->selectRaw('
                    AVG(nilai_teknik) as avg_teknik,
                    AVG(nilai_hafalan) as avg_hafalan,
                    AVG(nilai_ekspresi) as avg_ekspresi,
                    AVG(nilai_penampilan) as avg_penampilan,
                    AVG(nilai_kehadiran) as avg_kehadiran,
                    AVG(nilai_akhir) as avg_akhir
                ')
                ->first();
                
            $data = [
                'total_pagelaran' => $totalPagelaran,
                'avg_teknik' => round($summary->avg_teknik, 1),
                'avg_hafalan' => round($summary->avg_hafalan, 1),
                'avg_ekspresi' => round($summary->avg_ekspresi, 1),
                'avg_penampilan' => round($summary->avg_penampilan, 1),
                'avg_kehadiran' => round($summary->avg_kehadiran, 1),
                'avg_akhir' => round($summary->avg_akhir, 1),
                'predikat_umum' => match(true) {
                    $summary->avg_akhir >= 90 => 'Istimewa',
                    $summary->avg_akhir >= 80 => 'Sangat Baik',
                    $summary->avg_akhir >= 70 => 'Baik',
                    $summary->avg_akhir >= 60 => 'Cukup',
                    default => 'Perlu Peningkatan',
                }
            ];
            
            return response()->json(['data' => $data]);
        });

        Route::post('/attendance/scan', [AttendanceController::class, 'processScan']);

        Route::get('/pendaftaran', function (Illuminate\Http\Request $req) {
            $user = $req->user();
            if ($user->tipe_anggota === 'pengunjung') {
                $data = PendaftaranTari::with(['tarian', 'jadwal'])
                    ->where('user_id', $user->id)
                    ->get();
            } else {
                $data = PendaftaranTari::with(['tarian', 'jadwal'])
                    ->where('user_id', $user->id)
                    ->where('status', 'aktif')
                    ->get();
            }
            return response()->json(['data' => $data]);
        });

        Route::post('/pendaftaran', [\App\Http\Controllers\BookingController::class, 'store']);

        Route::post('/pendaftaran/{id}/batalkan', function (Illuminate\Http\Request $req, $id) {
            $p = PendaftaranTari::where('id', $id)->where('user_id', $req->user()->id)->firstOrFail();
            $p->update(['status' => 'nonaktif']);
            return response()->json(['success' => true, 'message' => 'Pendaftaran berhasil dibatalkan.']);
        });


        Route::get('/kehadiran-saya', function (Illuminate\Http\Request $req) {
            $data = Kehadiran::with(['jadwal', 'tarian'])
                ->where('user_id', $req->user()->id)
                ->orderByDesc('tanggal')
                ->paginate(20);
            return response()->json($data);
        });

        Route::get('/kehadiran-saya/statistik', function (Illuminate\Http\Request $req) {
            $bulan = $req->get('bulan', now()->format('Y-m'));
            $stats = Kehadiran::where('user_id', $req->user()->id)
                ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');
            $hadir = (int) ($stats['hadir'] ?? 0);
            $izin  = (int) ($stats['izin']  ?? 0);
            $alpa  = (int) ($stats['alpa']  ?? 0);
            $total = $hadir + $izin + $alpa;
            return response()->json(['data' => [
                'bulan' => $bulan, 'hadir' => $hadir, 'izin' => $izin, 'alpa' => $alpa,
                'total' => $total, 'persen_hadir' => $total > 0 ? round($hadir / $total * 100) : 0,
            ]]);
        });

        Route::get('/pengumuman', function () {
            $data = \App\Models\Pengumuman::orderBy('created_at', 'desc')->get();
            return response()->json(['data' => $data]);
        });

        // Ujian Midhang Sore API
        Route::post('/ujian/daftar', [UjianApiController::class, 'daftar']);
        Route::get('/ujian/status-saya', [UjianApiController::class, 'statusSaya']);
    });
});
