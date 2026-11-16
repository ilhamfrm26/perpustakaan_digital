<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun contoh: masing-masing role punya username & password sendiri
        User::create(['name' => 'Administrator',        'username' => 'admin',   'password' => 'admin123',   'role' => 'admin']);
        User::create(['name' => 'Petugas Perpustakaan', 'username' => 'petugas', 'password' => 'petugas123', 'role' => 'petugas']);
        User::create(['name' => 'Siswa Contoh',         'username' => 'pelajar', 'password' => 'pelajar123', 'role' => 'pelajar', 'kelas' => 'XI RPL']);

        $kat = [];
        foreach (['Novel', 'Pelajaran', 'Teknologi', 'Sejarah', 'Pengembangan Diri'] as $n) {
            $kat[$n] = Category::create(['nama' => $n])->id;
        }

        $buku = [
            ['Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Novel', 4],
            ['Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 'Novel', 3],
            ['Matematika Kelas XI', 'Tim Kemendikbud', 'Kemendikbud', 2021, 'Pelajaran', 6],
            ['Dasar Pemrograman Web', 'Budi Raharjo', 'Informatika', 2020, 'Teknologi', 5],
            ['Belajar Laravel untuk Pemula', 'Andi Wijaya', 'Elex Media', 2023, 'Teknologi', 3],
            ['Sejarah Indonesia Modern', 'M.C. Ricklefs', 'Serambi', 2008, 'Sejarah', 2],
            ['Atomic Habits (Terjemahan)', 'James Clear', 'Gramedia', 2019, 'Pengembangan Diri', 4],
            ['Filosofi Teras', 'Henry Manampiring', 'Kompas', 2018, 'Pengembangan Diri', 3],
        ];
        foreach ($buku as [$j, $p, $pb, $t, $k, $s]) {
            Book::create(['judul' => $j, 'penulis' => $p, 'penerbit' => $pb, 'tahun' => $t, 'category_id' => $kat[$k], 'stok' => $s]);
        }
    }
}
