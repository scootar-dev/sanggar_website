<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom tanggal_latihan dan jam_latihan untuk mendukung dynamic booking.
     */
    public function up(): void
    {
        Schema::table('pendaftaran_tari', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftaran_tari', 'tanggal_latihan')) {
                $table->date('tanggal_latihan')->nullable()->after('tarian_id');
            }
            if (!Schema::hasColumn('pendaftaran_tari', 'jam_latihan')) {
                $table->string('jam_latihan', 5)->nullable()->after('tanggal_latihan'); // Format H:i, e.g. "14:00"
            }
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_tari', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('pendaftaran_tari', 'tanggal_latihan')) {
                $columnsToDrop[] = 'tanggal_latihan';
            }
            if (Schema::hasColumn('pendaftaran_tari', 'jam_latihan')) {
                $columnsToDrop[] = 'jam_latihan';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
