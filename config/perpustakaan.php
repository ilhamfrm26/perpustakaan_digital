<?php

return [
    // Semua nilai bisa diubah lewat file .env
    'lama_pinjam'    => (int) env('LAMA_PINJAM', 7),        // hari
    'denda_per_hari' => (int) env('DENDA_PER_HARI', 1000),  // rupiah
    'maks_pinjam'    => (int) env('MAKS_PINJAM', 3),        // buku aktif per pelajar
    'rekening'       => env('REKENING_PERPUS', 'BCA 1234567890 a.n. Perpustakaan Sekolah'),
];
