<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('nama')->unique();
            $t->timestamps();
        });

        Schema::create('books', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $t->string('judul');
            $t->string('penulis');
            $t->string('penerbit')->nullable();
            $t->unsignedSmallInteger('tahun')->nullable();
            $t->unsignedInteger('stok')->default(1);
            $t->timestamps();
        });

        Schema::create('loans', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->foreignId('book_id')->constrained()->restrictOnDelete();
            $t->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            // diajukan | dipinjam | menunggu_kembali | dikembalikan | ditolak
            $t->string('status')->default('diajukan');
            $t->date('tgl_pinjam')->nullable();
            $t->date('tgl_jatuh_tempo')->nullable();
            $t->date('tgl_kembali')->nullable();
            $t->unsignedInteger('denda')->default(0);
            $t->timestamps();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('jumlah');
            $t->string('metode')->nullable();      // tunai | transfer | qris
            $t->string('status')->default('belum'); // belum | menunggu | lunas
            $t->timestamp('dibayar_pada')->nullable();
            $t->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('books');
        Schema::dropIfExists('categories');
    }
};
