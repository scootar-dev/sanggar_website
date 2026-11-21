<?php

namespace App\Http\Controllers;

use App\Models\{Tarian, JadwalLatihan, PendaftaranTari, Kehadiran};
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenjadwalanController extends Controller
{
    public function index()
    {
        $pendaftaran = PendaftaranTari::with(['tarian', 'jadwal'])->where('user_id', Auth::id())
            ->whereIn('status', ['aktif', 'pending'])->orderByDesc('created_at')->get();
        $tarianTersedia = Tarian::where('aktif', true)->orderBy('urutan')->get();
        $jadwalLatihan = JadwalLatihan::where('aktif', true)->orderBy('urutan')->get();
        return view('pages.penjadwalan', compact('pendaftaran', 'tarianTersedia', 'jadwalLatihan'));
    }

    public function daftar(Request $request, BookingService $booking)
    {
        $data = $request->validate([
            'tarian_id' => 'required|exists:tarian,id',
            'tanggal_latihan' => 'required|date|after_or_equal:today',
            'jam_latihan' => 'required|date_format:H:i',
            'catatan' => 'nullable|string|max:500',
        ]);
        $session = $booking->book($request->user(), $data);
        return back()->with('success', $session->status === 'pending'
            ? 'Booking terkirim. Menunggu konfirmasi admin.' : 'Pendaftaran sesi latihan berhasil.');
    }

    public function batalkan($id)
    {
        PendaftaranTari::where('user_id', Auth::id())->findOrFail($id)->update(['status' => 'nonaktif']);
        return back()->with('success', 'Pendaftaran berhasil dibatalkan.');
    }

    public function riwayatKehadiran()
    {
        $kehadiran = Kehadiran::with(['jadwal', 'tarian'])->where('user_id', Auth::id())
            ->orderByDesc('tanggal')->paginate(20);
        return view('pages.riwayat_kehadiran', compact('kehadiran'));
    }
}
