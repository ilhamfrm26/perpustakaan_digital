# Perpustakaan Sekolah (Laravel 11/12 + MySQL)

Folder ini adalah "overlay": isinya ditimpa ke proyek Laravel baru.

## Langkah pasang
1. `composer create-project laravel/laravel perpustakaan`
2. Salin SELURUH isi folder ini ke dalam folder `perpustakaan` (timpa jika diminta).
3. Buat database kosong `perpustakaan` di MySQL, lalu atur `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=perpustakaan
   DB_USERNAME=root
   DB_PASSWORD=
   APP_LOCALE=id
   # opsional
   LAMA_PINJAM=7
   DENDA_PER_HARI=1000
   MAKS_PINJAM=3
   REKENING_PERPUS="BCA 1234567890 a.n. Perpustakaan Sekolah"
   ```
4. `php artisan migrate:fresh --seed`
5. `php artisan serve` lalu buka http://127.0.0.1:8000

## Akun contoh
| Peran   | Username | Password    |
|---------|----------|-------------|
| Admin   | admin    | admin123    |
| Petugas | petugas  | petugas123  |
| Pelajar | pelajar  | pelajar123  |

Ganti password akun contoh sebelum dipakai sungguhan (menu Pengguna).
