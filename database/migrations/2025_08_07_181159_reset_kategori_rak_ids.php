<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        DB::table('kategoris')->truncate();
        DB::table('raks')->truncate();

        DB::statement('ALTER TABLE kategoris AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE raks AUTO_INCREMENT = 1');

        $this->seedKategoris();
        $this->seedRaks();

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(): void
    {

    }

    private function seedKategoris(): void
    {
        $kategoris = [
            ['id' => 1, 'kode_kategori' => '000', 'nama_kategori' => 'Komputer, Informasi dan Referensi umum', 'deskripsi' => 'Buku-buku tentang komputer, informasi, dan referensi umum'],
            ['id' => 2, 'kode_kategori' => '100', 'nama_kategori' => 'Filsafat dan Psikologi', 'deskripsi' => 'Buku-buku tentang filsafat dan psikologi'],
            ['id' => 3, 'kode_kategori' => '200', 'nama_kategori' => 'Agama', 'deskripsi' => 'Buku-buku tentang agama'],
            ['id' => 4, 'kode_kategori' => '300', 'nama_kategori' => 'Ilmu Sosial', 'deskripsi' => 'Buku-buku tentang ilmu sosial'],
            ['id' => 5, 'kode_kategori' => '400', 'nama_kategori' => 'Bahasa', 'deskripsi' => 'Buku-buku tentang bahasa'],
            ['id' => 6, 'kode_kategori' => '500', 'nama_kategori' => 'Sains dan Matematika', 'deskripsi' => 'Buku-buku tentang sains dan matematika'],
            ['id' => 7, 'kode_kategori' => '600', 'nama_kategori' => 'Teknologi', 'deskripsi' => 'Buku-buku tentang teknologi'],
            ['id' => 8, 'kode_kategori' => '700', 'nama_kategori' => 'Kesenian dan Rekreasi', 'deskripsi' => 'Buku-buku tentang kesenian dan rekreasi'],
            ['id' => 9, 'kode_kategori' => '800', 'nama_kategori' => 'Sastra', 'deskripsi' => 'Buku-buku tentang sastra'],
            ['id' => 10, 'kode_kategori' => '900', 'nama_kategori' => 'Sejarah dan Geografi', 'deskripsi' => 'Buku-buku tentang sejarah dan geografi']
        ];

        foreach ($kategoris as $kategori) {
            DB::table('kategoris')->insert($kategori);
        }
    }

    private function seedRaks(): void
    {
        $raks = [
            ['id' => 1, 'kode_rak' => 'R000', 'nama_rak' => 'Rak Komputer & Referensi', 'lokasi' => 'Rak 1 - Depan', 'kapasitas' => 100],
            ['id' => 2, 'kode_rak' => 'R100', 'nama_rak' => 'Rak Filsafat & Psikologi', 'lokasi' => 'Rak 1 - Belakang', 'kapasitas' => 100],
            ['id' => 3, 'kode_rak' => 'R200', 'nama_rak' => 'Rak Agama', 'lokasi' => 'Rak 2 - Depan', 'kapasitas' => 100],
            ['id' => 4, 'kode_rak' => 'R300', 'nama_rak' => 'Rak Ilmu Sosial', 'lokasi' => 'Rak 2 - Belakang', 'kapasitas' => 100],
            ['id' => 5, 'kode_rak' => 'R400', 'nama_rak' => 'Rak Bahasa', 'lokasi' => 'Rak 3 - Depan', 'kapasitas' => 100],
            ['id' => 6, 'kode_rak' => 'R500', 'nama_rak' => 'Rak Sains & Matematika', 'lokasi' => 'Rak 3 - Belakang', 'kapasitas' => 100],
            ['id' => 7, 'kode_rak' => 'R600', 'nama_rak' => 'Rak Teknologi', 'lokasi' => 'Rak 4 - Depan', 'kapasitas' => 100],
            ['id' => 8, 'kode_rak' => 'R700', 'nama_rak' => 'Rak Seni & Rekreasi', 'lokasi' => 'Rak 4 - Belakang', 'kapasitas' => 100],
            ['id' => 9, 'kode_rak' => 'R800', 'nama_rak' => 'Rak Sastra', 'lokasi' => 'Rak 5 - Depan', 'kapasitas' => 100],
            ['id' => 10, 'kode_rak' => 'R900', 'nama_rak' => 'Rak Sejarah & Geografi', 'lokasi' => 'Rak 5 - Belakang', 'kapasitas' => 100]
        ];

        foreach ($raks as $rak) {
            DB::table('raks')->insert($rak);
        }
    }
};
