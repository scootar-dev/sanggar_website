<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request, BookingService $service)
    {
        $data = $request->validate([
            'tarian_id' => 'required|exists:tarian,id',
            'tanggal_latihan' => 'required|date|after_or_equal:today',
            'jam_latihan' => 'required|date_format:H:i',
            'catatan' => 'nullable|string|max:500',
        ]);
        $booking = $service->book($request->user(), $data)->load('tarian');
        return response()->json(['success' => true, 'data' => $booking, 'message' => $booking->status === 'pending'
            ? 'Booking terkirim. Menunggu konfirmasi admin.' : 'Pendaftaran berhasil.'], 201);
    }

    public function storeGuest(Request $request, BookingService $service)
    {
        abort_unless($request->user()->tipe_anggota === 'pengunjung', 403);
        $data = $request->validate([
            'tarian_id' => 'required|exists:tarian,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam' => 'required|date_format:H:i',
        ]);
        $service->book($request->user(), ['tarian_id' => $data['tarian_id'], 'tanggal_latihan' => $data['tanggal'], 'jam_latihan' => $data['jam']]);
        return back()->with('success', 'Sesi latihan terkirim. Menunggu konfirmasi admin.');
    }
}
