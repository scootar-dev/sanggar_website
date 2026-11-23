<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:update {--email=wangiindriyaantahamahadi@gmail.com} {--password=wangi603338} {--name=Admin Sanggar Mulya Bhakti}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbarui kredensial akun administrator';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        $admin = User::where('role', 'admin')
            ->orWhere('email', 'admin@sanggarmulya.id')
            ->first();

        if ($admin) {
            $admin->update([
                'name'              => $name,
                'email'             => $email,
                'password'          => Hash::make($password),
                'role'              => 'admin',
                'status'            => 'aktif',
                'email_verified_at' => $admin->email_verified_at ?? now(),
            ]);
            $this->info("Akun admin berhasil diperbarui!");
        } else {
            $admin = User::create([
                'name'              => $name,
                'email'             => $email,
                'alamat'            => 'Indramayu, Jawa Barat',
                'password'          => Hash::make($password),
                'role'              => 'admin',
                'status'            => 'aktif',
                'email_verified_at' => now(),
            ]);
            $this->info("Akun admin baru berhasil dibuat!");
        }

        // Hapus duplikat email admin default lama jika ada
        User::where('email', 'admin@sanggarmulya.id')
            ->where('id', '!=', $admin->id)
            ->delete();

        $this->table(
            ['ID', 'Nama', 'Email', 'Role', 'Status'],
            [[$admin->id, $admin->name, $admin->email, $admin->role, $admin->status]]
        );

        return Command::SUCCESS;
    }
}

