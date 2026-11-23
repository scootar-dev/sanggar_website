<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cari admin yang ada atau user dengan email admin default lama
        $admin = User::where('role', 'admin')
            ->orWhere('email', 'admin@sanggarmulya.id')
            ->first();

        if ($admin) {
            $admin->update([
                'name'              => 'Admin Sanggar Mulya Bhakti',
                'email'             => 'wangiindriyaantahamahadi@gmail.com',
                'password'          => Hash::make('wangi603338'),
                'role'              => 'admin',
                'status'            => 'aktif',
                'email_verified_at' => $admin->email_verified_at ?? now(),
            ]);
        } else {
            User::create([
                'name'              => 'Admin Sanggar Mulya Bhakti',
                'email'             => 'wangiindriyaantahamahadi@gmail.com',
                'alamat'            => 'Indramayu, Jawa Barat',
                'password'          => Hash::make('wangi603338'),
                'role'              => 'admin',
                'status'            => 'aktif',
                'email_verified_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu di-rollback untuk menjaga integritas kredensial
    }
};

