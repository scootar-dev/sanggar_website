<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestCode;

class TestCodeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Kode Uji Coba Alpha',
                'kode' => 'TEST-001',
                'keterangan' => 'Uji coba integrasi sistem database Sanggar Mulya Bhakti.',
                'status' => 'Aktif',
            ],
            [
                'nama' => 'Kode Uji Coba Beta',
                'kode' => 'TEST-002',
                'keterangan' => 'Pengujian modul pendaftaran dan penilai pementasan.',
                'status' => 'Aktif',
            ],
            [
                'nama' => 'Kode Uji Coba Gamma',
                'kode' => 'TEST-003',
                'keterangan' => 'Kode sampel untuk verifikasi data pengujian.',
                'status' => 'Nonaktif',
            ],
            [
                'nama' => 'Kode Uji Coba Delta',
                'kode' => 'TEST-004',
                'keterangan' => 'Data sampel pengujian performa sistem.',
                'status' => 'Aktif',
            ],
        ];

        foreach ($data as $item) {
            TestCode::updateOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
