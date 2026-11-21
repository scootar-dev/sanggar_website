<?php

namespace App\Services;

use App\Models\PendaftaranTari;
use App\Models\Tarian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function book(User $user, array $data): PendaftaranTari
    {
        return DB::transaction(function () use ($user, $data) {
            // Serialize capacity checks across all dances, including empty slots.
            Tarian::orderBy('id')->lockForUpdate()->get(['id']);
            $date = Carbon::parse($data['tanggal_latihan'])->toDateString();
            $time = $data['jam_latihan'];
            if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time) || Carbon::parse("{$date} {$time}")->isPast()) {
                throw ValidationException::withMessages(['jam_latihan' => 'Pilih tanggal dan jam latihan yang belum lewat (HH:MM).']);
            }
            if (!Tarian::whereKey($data['tarian_id'])->where('aktif', true)->exists()) {
                throw ValidationException::withMessages(['tarian_id' => 'Tarian tidak aktif.']);
            }
            $this->checkCapacity($user->id, $data['tarian_id'], $date, $time);
            $booking = PendaftaranTari::create([
                'user_id' => $user->id,
                'tarian_id' => $data['tarian_id'],
                'tanggal_latihan' => $date,
                'jam_latihan' => $time,
                'status' => $user->tipe_anggota === 'pengunjung' ? 'pending' : 'aktif',
                'tanggal_daftar' => now(),
                'catatan' => $data['catatan'] ?? null,
            ]);
            if ($user->tipe_anggota === 'pengunjung') {
                $expires = Carbon::parse($date)->addDays(3);
                if (!$user->tgl_kadaluarsa || $expires->gt($user->tgl_kadaluarsa)) {
                    $user->update(['tgl_kadaluarsa' => $expires]);
                }
            }
            return $booking;
        });
    }

    public function confirm(int $id): PendaftaranTari
    {
        return DB::transaction(function () use ($id) {
            Tarian::orderBy('id')->lockForUpdate()->get(['id']);
            $booking = PendaftaranTari::with(['user', 'tarian'])->lockForUpdate()->findOrFail($id);
            if ($booking->status !== 'pending') {
                throw ValidationException::withMessages(['booking' => 'Hanya booking menunggu yang dapat dikonfirmasi.']);
            }
            $this->checkCapacity($booking->user_id, $booking->tarian_id, $booking->tanggal_latihan->toDateString(), $booking->jam_latihan, $booking->id);
            $booking->update(['status' => 'aktif']);
            return $booking;
        });
    }

    private function checkCapacity(int $userId, int $danceId, string $date, string $time, ?int $except = null): void
    {
        $sessions = PendaftaranTari::whereDate('tanggal_latihan', $date)
            ->where('jam_latihan', $time)->whereIn('status', ['aktif', 'pending'])
            ->when($except, fn ($query) => $query->where('id', '!=', $except))->get();
        if ($sessions->where('user_id', $userId)->isNotEmpty()) {
            throw ValidationException::withMessages(['jam_latihan' => 'Anda sudah memiliki booking pada tanggal dan jam ini.']);
        }
        $dances = $sessions->pluck('tarian_id')->unique();
        if ($dances->count() >= 2 && !$dances->contains($danceId)) {
            throw ValidationException::withMessages(['jam_latihan' => 'Aula penuh: maksimal dua tarian dalam satu jam.']);
        }
        if ($sessions->where('tarian_id', $danceId)->count() >= 5) {
            throw ValidationException::withMessages(['jam_latihan' => 'Kelompok penuh: maksimal lima orang per tarian.']);
        }
    }
}
