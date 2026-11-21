<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    ProfileController,
    EventController,
    DigitalArchiveController,
    DashboardController,
    PenjadwalanController,
    AttendanceController,
    ChatbotController,
};
use App\Http\Controllers\Api\GeminiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\{
    DashboardController     as AdminDashboard,
    ProfileController       as AdminProfile,
    EventController         as AdminEvent,
    TarianController        as AdminTarian,
    AnggotaController        as AdminAnggota,
    GaleriController         as AdminGaleri,
    KehadiranController      as AdminKehadiran,
    TopengController         as AdminTopeng,
    BookingController        as AdminBooking,
    PengumumanController     as AdminPengumuman,
    BackupController,
};

// ── PUBLIC ────────────────────────────────────────────────────
Route::get('/',               [HomeController::class,           'index'])->name('home');
Route::get('/profile',        [ProfileController::class,        'index'])->name('profile');
Route::get('/event',          [EventController::class,          'index'])->name('event');
Route::post('/event/ajukan',  [EventController::class,          'ajukan'])->name('event.ajukan');
Route::post('/event/daftar',  [EventController::class,          'daftar'])->name('event.daftar');
Route::get('/event/tiket/{order_id}', [EventController::class,  'tiketPdf'])->name('event.tiket');
Route::get('/digital-archive',[DigitalArchiveController::class, 'index'])->name('digital-archive');
Route::get('/galeri/{seksi?}', [App\Http\Controllers\GaleriController::class, 'frontendIndex'])->name('galeri.frontend.index');

// ── TAMU (Public Guest Log) ──────────────────────────────────
Route::get('/tamu',           [\App\Http\Controllers\TamuController::class, 'index'])->name('tamu.index');
Route::post('/tamu',          [\App\Http\Controllers\TamuController::class, 'store'])->name('tamu.store');

// ── AUTH ──────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register',[AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.post');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// OTP Email Verification
Route::get('/otp/verify',   [AuthController::class, 'showOtpForm'])->name('otp.verify.form');
Route::post('/otp/verify',  [AuthController::class, 'verifyOtp'])->name('otp.verify')->middleware('throttle:5,1');
Route::post('/otp/resend',  [AuthController::class, 'resendOtp'])->name('otp.resend')->middleware('throttle:1,1');

// Google OAuth (Web)
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->middleware('throttle:5,1')->name('password.store');
});


// ── MEMBER (harus login) ──────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/perkembangan', [\App\Http\Controllers\MemberProgressController::class, 'index'])->name('member.progress');
    Route::get('/dashboard',                  [DashboardController::class,   'index'])->name('dashboard');
    Route::post('/dashboard/tambah-sesi',     [\App\Http\Controllers\BookingController::class, 'storeGuest'])->name('dashboard.tambah-sesi');
    Route::get('/penjadwalan',                [PenjadwalanController::class, 'index'])->name('penjadwalan');
    Route::post('/penjadwalan/daftar',        [PenjadwalanController::class, 'daftar'])->name('penjadwalan.daftar');
    Route::post('/penjadwalan/batalkan/{id}', [PenjadwalanController::class, 'batalkan'])->name('penjadwalan.batalkan');
    Route::get('/penjadwalan/riwayat',        [PenjadwalanController::class, 'riwayatKehadiran'])->name('penjadwalan.kehadiran');
    
    // Member Attendance Scanner
    Route::post('/kehadiran/scan/process',    [AttendanceController::class, 'processScan'])->name('member.kehadiran.process');
    
    // Member Profile
    Route::get('/my-profile',                 [DashboardController::class, 'editProfile'])->name('member.profile');
    Route::post('/my-profile/update',         [DashboardController::class, 'updateProfile'])->name('member.profile.update');

    // Change password for member
    Route::post('/member/password', [DashboardController::class, 'updatePassword'])
        ->name('member.password.update');

    // Check password for member
    Route::post('/member/password/check', [DashboardController::class, 'checkCurrentPassword'])
        ->name('member.password.check');

    // Ujian Midhang Sore
    Route::post('/ujian/daftar',              [\App\Http\Controllers\UjianController::class, 'daftar'])->name('ujian.daftar');
    Route::get('/ujian/sertifikat/{id}',      [\App\Http\Controllers\UjianController::class, 'downloadSertifikat'])->name('ujian.sertifikat');
});


// ── ADMIN ─────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Profil, Pelatih, Pengelola, Jadwal
    Route::get('/profil',                        [AdminProfile::class, 'index'])->name('profil.index');
    Route::post('/profil/update',                [AdminProfile::class, 'updateProfil'])->name('profil.update');
    Route::post('/profil/social', [AdminProfile::class, 'updateSocial'])->name('profil.social');

    Route::post('/profil/pelatih',               [AdminProfile::class, 'storePelatih'])->name('pelatih.store');
    Route::post('/profil/pelatih/{id}/update',   [AdminProfile::class, 'updatePelatih'])->name('pelatih.update');
    Route::post('/profil/pelatih/{id}/delete',   [AdminProfile::class, 'destroyPelatih'])->name('pelatih.destroy');

    Route::post('/profil/pengelola',             [AdminProfile::class, 'storePengelola'])->name('pengelola.store');
    Route::post('/profil/pengelola/{id}/update', [AdminProfile::class, 'updatePengelola'])->name('pengelola.update');
    Route::post('/profil/pengelola/{id}/delete', [AdminProfile::class, 'destroyPengelola'])->name('pengelola.destroy');

    Route::post('/profil/jadwal',                [AdminProfile::class, 'storeJadwal'])->name('jadwal.store');
    Route::post('/profil/jadwal/{id}/update',    [AdminProfile::class, 'updateJadwal'])->name('jadwal.update');
    Route::post('/profil/jadwal/{id}/delete',    [AdminProfile::class, 'destroyJadwal'])->name('jadwal.destroy');

    // Event — pakai {id} numerik bukan model binding
    Route::prefix('event')->name('event.')->group(function () {
        Route::get('/',              [App\Http\Controllers\Admin\EventController::class, 'index'])->name('index');
        Route::get('/create',        [App\Http\Controllers\Admin\EventController::class, 'create'])->name('create');
        Route::post('/',             [App\Http\Controllers\Admin\EventController::class, 'store'])->name('store');
        Route::get('/{id}/edit',     [App\Http\Controllers\Admin\EventController::class, 'edit'])->name('edit');
        Route::put('/{id}',          [App\Http\Controllers\Admin\EventController::class, 'update'])->name('update');
        Route::delete('/{id}',       [App\Http\Controllers\Admin\EventController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [App\Http\Controllers\Admin\EventController::class, 'approve'])->name('approve');
        
        // Data Peserta
        Route::get('/peserta',       [App\Http\Controllers\Admin\PesertaEventController::class, 'index'])->name('peserta.index');
        Route::put('/peserta/{id}',  [App\Http\Controllers\Admin\PesertaEventController::class, 'updateStatus'])->name('peserta.update');
        Route::delete('/peserta/{id}',[App\Http\Controllers\Admin\PesertaEventController::class, 'destroy'])->name('peserta.destroy');
    });

    // Midtrans Webhook

    // Tarian
    Route::get('/tarian/pdf',          [AdminTarian::class, 'downloadPdf'])->name('tarian.pdf');
    Route::get('/tarian',              [AdminTarian::class, 'index'])->name('tarian.index');
    Route::get('/tarian/create',       [AdminTarian::class, 'create'])->name('tarian.create');
    Route::post('/tarian',             [AdminTarian::class, 'store'])->name('tarian.store');
    Route::get('/tarian/{id}/edit',    [AdminTarian::class, 'edit'])->name('tarian.edit');
    Route::put('/tarian/{id}',         [AdminTarian::class, 'update'])->name('tarian.update');
    Route::delete('/tarian/{id}/delete',[AdminTarian::class, 'destroy'])->name('tarian.destroy');

    // Topeng
    Route::get('/topeng',              [AdminTopeng::class, 'index'])->name('topeng.index');
    Route::get('/topeng/create',       [AdminTopeng::class, 'create'])->name('topeng.create');
    Route::post('/topeng',             [AdminTopeng::class, 'store'])->name('topeng.store');
    Route::get('/topeng/{id}/edit',    [AdminTopeng::class, 'edit'])->name('topeng.edit');
    Route::put('/topeng/{id}',         [AdminTopeng::class, 'update'])->name('topeng.update');
    Route::delete('/topeng/{id}/delete',[AdminTopeng::class, 'destroy'])->name('topeng.destroy');

    // Anggota
    Route::get('/anggota/pdf',            [AdminAnggota::class, 'downloadPdf'])->name('anggota.pdf');
    Route::get('/anggota/excel',           [AdminAnggota::class, 'downloadExcel'])->name('anggota.excel');
    Route::get('/anggota',                [AdminAnggota::class, 'index'])->name('anggota.index');
    Route::get('/anggota/create',         [AdminAnggota::class, 'create'])->name('anggota.create');
    Route::post('/anggota',               [AdminAnggota::class, 'store'])->name('anggota.store');
    Route::get('/anggota/{id}/edit',      [AdminAnggota::class, 'edit'])->name('anggota.edit');
    Route::put('/anggota/{id}',           [AdminAnggota::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{id}/delete', [AdminAnggota::class, 'destroy'])->name('anggota.destroy');
    Route::patch('/anggota/{id}/toggle',  [AdminAnggota::class, 'toggleStatus'])->name('anggota.toggle');

    // Galeri
    Route::get('/galeri',              [AdminGaleri::class, 'index'])->name('galeri.index');
    Route::post('/galeri',             [AdminGaleri::class, 'store'])->name('galeri.store');
    Route::delete('/galeri/{id}',      [AdminGaleri::class, 'destroy'])->name('galeri.destroy');

    // Kehadiran
    Route::get('/kehadiran/booking', [\App\Http\Controllers\Admin\BookingAttendanceController::class, 'index'])->name('kehadiran.booking');
    Route::post('/kehadiran/booking', [\App\Http\Controllers\Admin\BookingAttendanceController::class, 'store'])->name('kehadiran.booking.store');
    Route::get('/kehadiran',                    [AdminKehadiran::class, 'index'])->name('kehadiran.index');
    Route::post('/kehadiran/input',             [AdminKehadiran::class, 'inputKehadiran'])->name('kehadiran.input');
    Route::post('/kehadiran/simpan',            [AdminKehadiran::class, 'simpanKehadiran'])->name('kehadiran.simpan');
    // Laporan Kehadiran
    Route::get('/kehadiran/laporan',            [AdminKehadiran::class, 'laporan'])->name('kehadiran.laporan');
    Route::get('/kehadiran/laporan/anggota',    [AdminKehadiran::class, 'laporanAnggota'])->name('kehadiran.laporan.anggota');
    Route::get('/kehadiran/laporan/pengunjung', [AdminKehadiran::class, 'laporanPengunjung'])->name('kehadiran.laporan.pengunjung');
    Route::get('/kehadiran/laporan/export-pdf', [AdminKehadiran::class, 'exportPdf'])->name('kehadiran.pdf');
    Route::get('/kehadiran/dynamic-qr-token',   [AdminKehadiran::class, 'generateDynamicToken'])->name('kehadiran.dynamic-qr-token');

    // Permanent QR (Kelas Barcode)
    Route::get('/kehadiran/permanent/{id}',     [AdminKehadiran::class, 'showPermanentQR'])->name('kehadiran.permanent.show');
    Route::delete('/kehadiran/permanent/{id}',  [AdminKehadiran::class, 'deletePermanentQR'])->name('kehadiran.permanent.destroy');

    // Booking Anggota Sementara
    Route::get('/booking',            [AdminBooking::class, 'index'])->name('booking.index');
    Route::get('/booking/pending-count', [AdminBooking::class, 'getPendingCount'])->name('booking.pending-count');
    Route::post('/booking/{id}/confirm', [AdminBooking::class, 'confirm'])->name('booking.confirm');
    Route::post('/booking/{id}/reject',  [AdminBooking::class, 'reject'])->name('booking.reject');
    Route::delete('/booking/{id}',    [AdminBooking::class, 'destroy'])->name('booking.destroy');

    // Broadcast Pengumuman Global
    Route::get('/pengumuman',         [AdminPengumuman::class, 'index'])->name('pengumuman.index');
    Route::post('/pengumuman',        [AdminPengumuman::class, 'store'])->name('pengumuman.store');
    Route::delete('/pengumuman/{id}', [AdminPengumuman::class, 'destroy'])->name('pengumuman.destroy');

    // Rapor Pagelaran
    Route::prefix('rapor')->name('rapor.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RaporPagelaranController::class, 'index'])->name('index');
        Route::get('/pagelaran/{eventId}', [\App\Http\Controllers\Admin\RaporPagelaranController::class, 'form'])->name('form');
        Route::post('/store', [\App\Http\Controllers\Admin\RaporPagelaranController::class, 'store'])->name('store');
        Route::get('/murid/{userId}', [\App\Http\Controllers\Admin\RaporPagelaranController::class, 'show'])->name('murid');
    });

    // Admin Ujian
    Route::prefix('ujian')->name('ujian.')->group(function () {
        Route::get('/{eventId}',                  [\App\Http\Controllers\Admin\AdminUjianController::class, 'index'])->name('index');
        Route::patch('/{id}/status',              [\App\Http\Controllers\Admin\AdminUjianController::class, 'updateStatus'])->name('update-status');
        Route::get('/{eventId}/nilai',            [\App\Http\Controllers\Admin\AdminUjianController::class, 'formNilai'])->name('form-nilai');
        Route::post('/nilai/simpan',              [\App\Http\Controllers\Admin\AdminUjianController::class, 'simpanNilai'])->name('simpan-nilai');
    });

    // Backup & Restore
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/run', [BackupController::class, 'backup'])->name('backup.run');
    Route::post('/backup/restore/{filename}', [BackupController::class, 'restore'])->name('backup.restore')->where('filename', '.*');
    Route::delete('/backup/delete/{filename}', [BackupController::class, 'delete'])->name('backup.delete')->where('filename', '.*');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download')->where('filename', '.*');
});

// ── CHATBOT AI ────────────────────────────────────────────────────────
Route::post('/admin/midtrans/webhook', [EventController::class, 'midtransWebhook'])->name('admin.midtrans.webhook');
Route::post('/chatbot/chat',      [ChatbotController::class, 'chat'])->middleware('throttle:20,1')->name('chatbot.chat');
Route::post('/chatbot/clear',     [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
Route::post('/chatbot/recommend', [ChatbotController::class, 'recommendDance'])->middleware('throttle:10,1')->name('chatbot.recommend');

// Fallback route jika symbolic link dinonaktifkan oleh penyedia hosting
Route::get('/storage/{path}', [\App\Http\Controllers\PublicFileController::class, 'show'])->where('path', '.*');
