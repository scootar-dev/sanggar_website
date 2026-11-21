<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Kehadiran, PendaftaranTari};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['tanggal' => 'nullable|date']);
        $tanggal = $data['tanggal'] ?? now()->toDateString();
        $bookings = PendaftaranTari::with(['user', 'tarian'])->whereDate('tanggal_latihan', $tanggal)
            ->where('status', 'aktif')->orderBy('jam_latihan')->get();
        $existing = Kehadiran::whereIn('pendaftaran_tari_id', $bookings->pluck('id'))->get()->keyBy('pendaftaran_tari_id');
        return view('admin.kehadiran.booking', compact('bookings', 'existing', 'tanggal'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'kehadiran' => 'required|array',
            'kehadiran.*' => 'required|in:hadir,izin,alpa',
        ]);
        DB::transaction(function () use ($data, $request) {
            foreach ($data['kehadiran'] as $id => $status) {
                $booking = PendaftaranTari::whereDate('tanggal_latihan', $data['tanggal'])
                    ->where('status', 'aktif')->lockForUpdate()->findOrFail($id);
                Kehadiran::updateOrCreate(['pendaftaran_tari_id' => $booking->id], [
                    'user_id' => $booking->user_id,
                    'tarian_id' => $booking->tarian_id,
                    'jadwal_id' => null,
                    'tanggal' => $data['tanggal'],
                    'status' => $status,
                    'metode_absen' => 'manual',
                    'dicatat_oleh' => $request->user()->name,
                ]);
            }
        });
        return back()->with('success', 'Kehadiran sesi booking berhasil disimpan.');
    }
}
