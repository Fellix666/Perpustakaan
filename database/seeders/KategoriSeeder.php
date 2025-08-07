<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'kode_kategori' => '000',
                'nama_kategori' => 'Komputer, Informasi dan Referensi umum',
                'deskripsi' => 'Buku-buku tentang komputer, informasi, dan referensi umum'
            ],
            [
                'kode_kategori' => '100',
                'nama_kategori' => 'Filsafat dan Psikologi',
                'deskripsi' => 'Buku-buku tentang filsafat dan psikologi'
            ],
            [
                'kode_kategori' => '200',
                'nama_kategori' => 'Agama',
                'deskripsi' => 'Buku-buku tentang agama'
            ],
            [
                'kode_kategori' => '300',
                'nama_kategori' => 'Ilmu Sosial',
                'deskripsi' => 'Buku-buku tentang ilmu sosial'
            ],
            [
                'kode_kategori' => '400',
                'nama_kategori' => 'Bahasa',
                'deskripsi' => 'Buku-buku tentang bahasa'
            ],
            [
                'kode_kategori' => '500',
                'nama_kategori' => 'Sains dan Matematika',
                'deskripsi' => 'Buku-buku tentang sains dan matematika'
            ],
            [
                'kode_kategori' => '600',
                'nama_kategori' => 'Teknologi',
                'deskripsi' => 'Buku-buku tentang teknologi'
            ],
            [
                'kode_kategori' => '700',
                'nama_kategori' => 'Kesenian dan Rekreasi',
                'deskripsi' => 'Buku-buku tentang kesenian dan rekreasi'
            ],
            [
                'kode_kategori' => '800',
                'nama_kategori' => 'Sastra',
                'deskripsi' => 'Buku-buku tentang sastra'
            ],
            [
                'kode_kategori' => '900',
                'nama_kategori' => 'Sejarah dan Geografi',
                'deskripsi' => 'Buku-buku tentang sejarah dan geografi'
            ]
        ];

        foreach ($kategoris as $kategori) {
            Kategori::firstOrCreate(
                ['kode_kategori' => $kategori['kode_kategori']],
                $kategori
            );
        }
    }
}
