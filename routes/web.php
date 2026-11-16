<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Semua role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/buku', [BookController::class, 'index'])->name('books.index');
    Route::get('/pinjaman', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/pembayaran', [PaymentController::class, 'index'])->name('payments.index');

    // Pelajar
    Route::middleware('role:pelajar')->group(function () {
        Route::post('/buku/{book}/pinjam', [LoanController::class, 'store'])->name('loans.store');
        Route::post('/pinjaman/{loan}/kembalikan', [LoanController::class, 'requestReturn'])->name('loans.requestReturn');
        Route::post('/pembayaran/{payment}/bayar', [PaymentController::class, 'pay'])->name('payments.pay');
    });

    // Petugas & Admin
    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/buku/tambah', [BookController::class, 'create'])->name('books.create');
        Route::post('/buku', [BookController::class, 'store'])->name('books.store');
        Route::get('/buku/{book}/ubah', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/buku/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/buku/{book}', [BookController::class, 'destroy'])->name('books.destroy');

        Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::post('/pinjaman/{loan}/setujui', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('/pinjaman/{loan}/tolak', [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('/pinjaman/{loan}/terima', [LoanController::class, 'processReturn'])->name('loans.return');

        Route::post('/pembayaran/{payment}/konfirmasi', [PaymentController::class, 'confirm'])->name('payments.confirm');
    });

    // Admin saja
    Route::middleware('role:admin')->group(function () {
        Route::resource('pengguna', UserController::class)
            ->except(['show'])->parameters(['pengguna' => 'user'])->names('users');
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    });
});
