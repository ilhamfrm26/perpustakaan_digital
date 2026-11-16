<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        $q = Payment::with(['loan.book', 'user'])->orderByRaw("FIELD(status,'belum','menunggu','lunas')")->latest();

        if ($u->role === 'pelajar') {
            $q->where('user_id', $u->id);
        }

        return view('payments.index', ['payments' => $q->simplePaginate(10)]);
    }

    /** Pelajar memilih cara bayar -> menunggu konfirmasi petugas. */
    public function pay(Request $request, Payment $payment)
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        if ($payment->status !== 'belum') {
            return back()->with('error', 'Tagihan ini sudah diproses.');
        }
        $data = $request->validate(['metode' => ['required', Rule::in(['tunai', 'transfer', 'qris'])]]);
        $payment->update(['metode' => $data['metode'], 'status' => 'menunggu']);

        return back()->with('success', 'Pembayaran dicatat. Petugas akan mengonfirmasi setelah menerima pembayaran Anda.');
    }

    /** Petugas memastikan uang sudah diterima -> lunas. */
    public function confirm(Request $request, Payment $payment)
    {
        if ($payment->status === 'lunas') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }
        $data = $request->validate(['metode' => ['required', Rule::in(['tunai', 'transfer', 'qris'])]]);
        $payment->update([
            'metode'       => $data['metode'],
            'status'       => 'lunas',
            'dibayar_pada' => now(),
            'petugas_id'   => $request->user()->id,
        ]);

        return back()->with('success', 'Pembayaran dikonfirmasi lunas.');
    }
}
