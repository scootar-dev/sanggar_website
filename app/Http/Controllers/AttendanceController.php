<?php

namespace App\Http\Controllers;

use App\Models\{Kehadiran, PendaftaranTari};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // ── Member Attendance Scanner ──────────────────────────────────
    public function processScan(Request $request)
    {
        $request->validate(['barcode_token' => 'required|string|max:500']);
        $token = $request->barcode_token;
        $user = Auth::user();

        $isDynamic = false;
        $decoded = base64_decode($token, true);
        if ($decoded) {
            $parts = explode('|', $decoded);
            if (count($parts) === 3 && $parts[0] === 'DYN-QR') {
                $isDynamic = true;
                $timestamp = (int)$parts[1];
                $signature = $parts[2];
                
                // 1. Validasi Signature HMAC
                $expectedSignature = hash_hmac('sha256', "DYN-QR|" . $timestamp, config('app.key'));
                if (!hash_equals($expectedSignature, $signature)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR Code tidak valid atau telah dimodifikasi.'
                    ], 400);
                }
                
                // 2. Validasi Batas Waktu Kadaluarsa (15 Detik)
                $diff = time() - $timestamp;
                if ($diff > 15 || $diff < -5) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR Code sudah kadaluarsa. Silakan scan QR Code terbaru di layar admin.'
                    ], 400);
                }
            }
        }

        if ($isDynamic) {
            $today = now()->toDateString();
            
            // Cari jadwal latihan hari ini yang belum di-absen
            $pendaftaran = PendaftaranTari::where('user_id', $user->id)
                ->whereDate('tanggal_latihan', $today)
                ->where('status', 'aktif')
                ->whereNotExists(function ($query) use ($today, $user) {
                    $query->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('kehadiran')
                          ->whereColumn('kehadiran.pendaftaran_tari_id', 'pendaftaran_tari.id')
                          ->whereDate('kehadiran.tanggal', $today)
                          ->where('kehadiran.user_id', $user->id);
                })
                ->where('jam_latihan', '<=', now()->format('H:i'))
                ->orderByDesc('jam_latihan')
                ->get()->first(fn ($booking) => \Carbon\Carbon::parse($today.' '.$booking->jam_latihan)
                    ->addMinutes(max(1, config('attendance.scan_window_minutes', 60)))->gte(now()));

            if (!$pendaftaran) {
                // Cek apakah sebenarnya punya jadwal tapi sudah absen semua
                $hasSession = PendaftaranTari::where('user_id', $user->id)
                    ->whereDate('tanggal_latihan', $today)
                    ->where('status', 'aktif')
                    ->exists();
                    
                if ($hasSession) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sesi sudah diabsen, belum dimulai, atau batas waktu scan telah lewat. Hubungi admin untuk koreksi absensi.'
                    ], 400);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki jadwal latihan yang aktif pada hari ini.'
                    ], 400);
                }
            }

            $sesi_tanggal = $today;
            $jadwal_id = $pendaftaran->jadwal_id;
            $tarian_id = $pendaftaran->tarian_id;

        } else {
            return response()->json(['success' => false, 'message' => 'Gunakan QR dinamis terbaru pada layar admin.'], 400);
        }

        // Simpan Kehadiran
        $attendance = Kehadiran::firstOrCreate(['pendaftaran_tari_id' => $pendaftaran->id], [
            'user_id'       => $user->id,
            'jadwal_id'     => $jadwal_id,
            'tarian_id'     => $tarian_id,
            'tanggal'       => $sesi_tanggal,
            'status'        => 'hadir',
            'metode_absen'  => 'barcode',
            'barcode_token' => $token,
            'scan_at'       => now(),
        ]);
        if (!$attendance->wasRecentlyCreated) {
            return response()->json(['success' => false, 'message' => 'Sesi ini sudah diabsen.'], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kehadiran berhasil dicatat! Selamat berlatih.'
        ]);
    }
}
