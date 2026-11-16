<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        $q = Loan::with(['book', 'user', 'payment'])->latest();

        if ($u->role === 'pelajar') {
            $q->where('user_id', $u->id);
        } elseif ($request->status) {
            $q->where('status', $request->status);
        }

        return view('loans.index', [
            'loans'  => $q->simplePaginate(10)->withQueryString(),
            'status' => $request->status,
        ]);
    }

    /** Pelajar mengajukan pinjaman. */
    public function store(Request $request, Book $book)
    {
        $u = $request->user();

        if (Payment::where('user_id', $u->id)->where('status', '!=', 'lunas')->exists()) {
            return back()->with('error', 'Lunasi denda Anda terlebih dahulu sebelum meminjam lagi.');
        }

        $aktif = Loan::where('user_id', $u->id)->whereIn('status', ['diajukan', 'dipinjam', 'menunggu_kembali']);

        if ((clone $aktif)->count() >= config('perpustakaan.maks_pinjam')) {
            return back()->with('error', 'Batas pinjaman tercapai ('.config('perpustakaan.maks_pinjam').' buku). Kembalikan buku lebih dulu.');
        }
        if ((clone $aktif)->where('book_id', $book->id)->exists()) {
            return back()->with('error', 'Anda sudah meminjam atau mengajukan buku ini.');
        }
        if ($book->stok < 1) {
            return back()->with('error', 'Stok buku ini sedang habis.');
        }

        Loan::create(['user_id' => $u->id, 'book_id' => $book->id, 'status' => 'diajukan']);

        return redirect()->route('loans.index')->with('success', 'Pengajuan terkirim. Tunggu persetujuan petugas.');
    }

    /** Petugas menyetujui: stok berkurang, jatuh tempo dihitung. */
    public function approve(Request $request, Loan $loan)
    {
        if ($loan->status !== 'diajukan') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $ok = DB::transaction(function () use ($loan, $request) {
            $book = Book::lockForUpdate()->find($loan->book_id);
            if ($book->stok < 1) {
                return false;
            }
            $book->decrement('stok');
            $loan->update([
                'status'          => 'dipinjam',
                'petugas_id'      => $request->user()->id,
                'tgl_pinjam'      => today(),
                'tgl_jatuh_tempo' => today()->addDays((int) config('perpustakaan.lama_pinjam')),
            ]);

            return true;
        });

        return back()->with($ok ? 'success' : 'error', $ok ? 'Pinjaman disetujui. Serahkan bukunya kepada pelajar.' : 'Stok buku habis, pengajuan tidak bisa disetujui.');
    }

    public function reject(Request $request, Loan $loan)
    {
        if ($loan->status !== 'diajukan') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }
        $loan->update(['status' => 'ditolak', 'petugas_id' => $request->user()->id]);

        return back()->with('success', 'Pengajuan ditolak.');
    }

    /** Pelajar memberi tahu bahwa buku akan dikembalikan. */
    public function requestReturn(Request $request, Loan $loan)
    {
        if ($loan->user_id !== $request->user()->id || $loan->status !== 'dipinjam') {
            abort(403);
        }
        $loan->update(['status' => 'menunggu_kembali']);

        return back()->with('success', 'Silakan serahkan buku ke petugas untuk diperiksa.');
    }

    /** Petugas menerima buku: stok kembali, denda dihitung, tagihan dibuat bila terlambat. */
    public function processReturn(Request $request, Loan $loan)
    {
        if (! $loan->aktif()) {
            return back()->with('error', 'Buku ini tidak sedang dipinjam.');
        }

        DB::transaction(function () use ($loan, $request) {
            $denda = $loan->hitungDenda();
            Book::whereKey($loan->book_id)->increment('stok');
            $loan->update([
                'status'      => 'dikembalikan',
                'tgl_kembali' => today(),
                'denda'       => $denda,
                'petugas_id'  => $request->user()->id,
            ]);
            if ($denda > 0) {
                Payment::create(['loan_id' => $loan->id, 'user_id' => $loan->user_id, 'jumlah' => $denda, 'status' => 'belum']);
            }
        });

        $loan->refresh();
        $msg = $loan->denda > 0
            ? 'Buku diterima. Pelajar terlambat, denda Rp'.number_format($loan->denda, 0, ',', '.').' dicatat.'
            : 'Buku diterima tepat waktu. Tidak ada denda.';

        return back()->with('success', $msg);
    }
}
