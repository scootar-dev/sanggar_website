<?php
// app/Models/PendaftaranTari.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranTari extends Model
{
    use HasFactory;

    protected $table    = 'pendaftaran_tari';

    protected $fillable = [
        'user_id',
        'tarian_id',
        'jadwal_id',
        'tanggal_latihan',
        'jam_latihan',
        'status',
        'tanggal_daftar',
        'catatan',
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
        'tanggal_latihan' => 'date',
    ];

    // ── Relasi ────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tarian()
    {
        return $this->belongsTo(Tarian::class, 'tarian_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalLatihan::class, 'jadwal_id');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'user_id', 'user_id')
                    ->where('jadwal_id', $this->jadwal_id)
                    ->where('tarian_id', $this->tarian_id);
    }

    // ── Scope: hanya yang aktif ───────────────────────────────
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Otomatis tolak pendaftaran private sesi (khusus status pending) yang waktunya sudah terlewat
     */
    public static function autoRejectExpiredPending(): int
    {
        $today = now()->toDateString();
        $currentTime = now()->format('H:i');

        // 1. Yang tanggal latihannya sebelum hari ini
        $updated1 = static::where('status', 'pending')
            ->whereNotNull('tanggal_latihan')
            ->whereDate('tanggal_latihan', '<', $today)
            ->update(['status' => 'ditolak']);

        // 2. Yang tanggal latihannya hari ini tetapi jam latihannya sudah lewat
        $updated2 = static::where('status', 'pending')
            ->whereNotNull('tanggal_latihan')
            ->whereDate('tanggal_latihan', '=', $today)
            ->whereNotNull('jam_latihan')
            ->where('jam_latihan', '<=', $currentTime)
            ->update(['status' => 'ditolak']);

        return $updated1 + $updated2;
    }
}