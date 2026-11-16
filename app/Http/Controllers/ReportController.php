<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $q = Loan::with(['book', 'user', 'payment'])
            ->when($request->dari, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->sampai, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest();

        $ids = (clone $q)->pluck('id');
        $ringkas = [
            'total'        => $ids->count(),
            'dikembalikan' => (clone $q)->where('status', 'dikembalikan')->count(),
            'terlambat'    => (clone $q)->where('denda', '>', 0)->count(),
            'denda_lunas'  => Payment::whereIn('loan_id', $ids)->where('status', 'lunas')->sum('jumlah'),
            'denda_belum'  => Payment::whereIn('loan_id', $ids)->where('status', '!=', 'lunas')->sum('jumlah'),
        ];

        return view('reports.index', [
            'loans'   => $q->simplePaginate(20)->withQueryString(),
            'ringkas' => $ringkas,
        ]);
    }
}
