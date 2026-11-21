<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kehadiran', function (Blueprint $table) {
            $table->unsignedBigInteger('jadwal_id')->nullable()->change();
            $table->foreignId('pendaftaran_tari_id')->nullable()->unique()->constrained('pendaftaran_tari')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kehadiran', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pendaftaran_tari_id');
        });
    }
};
