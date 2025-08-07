<?php

namespace Database\Seeders;

use App\Models\Rak;
use Illuminate\Database\Seeder;

class RakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $raks = [
            // Rak 1 (Depan & Belakang)
            [
                'kode_rak' => 'R000',
                'nama_rak' => 'Rak Komputer & Referensi',
                'lokasi' => 'Rak 1 - Depan',
                'kapasitas' => 100
            ],
            [
                'kode_rak' => 'R100',
                'nama_rak' => 'Rak Filsafat & Psikologi',
                'lokasi' => 'Rak 1 - Belakang',
                'kapasitas' => 100
            ],
            // Rak 2 (Depan & Belakang)
            [
                'kode_rak' => 'R200',
                'nama_rak' => 'Rak Agama',
                'lokasi' => 'Rak 2 - Depan',
                'kapasitas' => 100
            ],
            [
                'kode_rak' => 'R300',
                'nama_rak' => 'Rak Ilmu Sosial',
                'lokasi' => 'Rak 2 - Belakang',
                'kapasitas' => 100
            ],
            // Rak 3 (Depan & Belakang)
            [
                'kode_rak' => 'R400',
                'nama_rak' => 'Rak Bahasa',
                'lokasi' => 'Rak 3 - Depan',
                'kapasitas' => 100
            ],
            [
                'kode_rak' => 'R500',
                'nama_rak' => 'Rak Sains & Matematika',
                'lokasi' => 'Rak 3 - Belakang',
                'kapasitas' => 100
            ],
            // Rak 4 (Depan & Belakang)
            [
                'kode_rak' => 'R600',
                'nama_rak' => 'Rak Teknologi',
                'lokasi' => 'Rak 4 - Depan',
                'kapasitas' => 100
            ],
            [
                'kode_rak' => 'R700',
                'nama_rak' => 'Rak Seni & Rekreasi',
                'lokasi' => 'Rak 4 - Belakang',
                'kapasitas' => 100
            ],
            // Rak 5 (Depan & Belakang)
            [
                'kode_rak' => 'R800',
                'nama_rak' => 'Rak Sastra',
                'lokasi' => 'Rak 5 - Depan',
                'kapasitas' => 100
            ],
            [
                'kode_rak' => 'R900',
                'nama_rak' => 'Rak Sejarah & Geografi',
                'lokasi' => 'Rak 5 - Belakang',
                'kapasitas' => 100
            ]
        ];

        foreach ($raks as $rak) {
            Rak::firstOrCreate(
                ['kode_rak' => $rak['kode_rak']],
                $rak
            );
        }
    }
}
