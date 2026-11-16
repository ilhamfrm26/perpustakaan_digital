<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');

        if ($u->role === 'admin') {
            $cards = [
                ['Judul buku', Book::count(), 'di katalog'],
                ['Pelajar terdaftar', User::where('role', 'pelajar')->count(), 'anggota'],
                ['Sedang dipinjam', Loan::whereIn('status', ['dipinjam', 'menunggu_kembali'])->count(), 'buku'],
                ['Denda terkumpul', $rp(Payment::where('status', 'lunas')->sum('jumlah')), 'sudah lunas'],
            ];
        } elseif ($u->role === 'petugas') {
            $cards = [
                ['Pengajuan baru', Loan::where('status', 'diajukan')->count(), 'perlu disetujui'],
                ['Sedang dipinjam', Loan::where('status', 'dipinjam')->count(), 'buku'],
                ['Pengembalian masuk', Loan::where('status', 'menunggu_kembali')->count(), 'perlu diterima'],
                ['Pembayaran masuk', Payment::where('status', 'menunggu')->count(), 'perlu dikonfirmasi'],
            ];
        } else {
            $aktif = Loan::where('user_id', $u->id)->whereIn('status', ['dipinjam', 'menunggu_kembali'])->get();
            $cards = [
                ['Buku dipinjam', $aktif->count(), 'maksimal '.config('perpustakaan.maks_pinjam')],
                ['Terlambat', $aktif->filter(fn ($l) => $l->hitungDenda() > 0)->count(), 'buku'],
                ['Denda belum dibayar', $rp(Payment::where('user_id', $u->id)->where('status', '!=', 'lunas')->sum('jumlah')), ''],
            ];
        }

        return view('dashboard', compact('cards'));
    }
}
