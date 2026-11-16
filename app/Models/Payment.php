<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['loan_id', 'user_id', 'jumlah', 'metode', 'status', 'dibayar_pada', 'petugas_id'];
    protected $casts = ['dibayar_pada' => 'datetime'];

    public function loan()    { return $this->belongsTo(Loan::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function petugas() { return $this->belongsTo(User::class, 'petugas_id'); }

    public function label(): string
    {
        return ['belum' => 'Belum dibayar', 'menunggu' => 'Menunggu konfirmasi', 'lunas' => 'Lunas'][$this->status] ?? $this->status;
    }

    public function badge(): string
    {
        return ['belum' => 'bad', 'menunggu' => 'warn', 'lunas' => 'ok'][$this->status] ?? 'info';
    }
}
