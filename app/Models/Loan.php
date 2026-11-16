<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id', 'book_id', 'petugas_id', 'status',
        'tgl_pinjam', 'tgl_jatuh_tempo', 'tgl_kembali', 'denda',
    ];

    protected $casts = [
        'tgl_pinjam'      => 'date',
        'tgl_jatuh_tempo' => 'date',
        'tgl_kembali'     => 'date',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function book()    { return $this->belongsTo(Book::class); }
    public function petugas() { return $this->belongsTo(User::class, 'petugas_id'); }
    public function payment() { return $this->hasOne(Payment::class); }

    public function aktif(): bool
    {
        return in_array($this->status, ['dipinjam', 'menunggu_kembali'], true);
    }

    /** Denda = jumlah hari terlambat x tarif per hari. */
    public function hitungDenda(): int
    {
        if (! $this->tgl_jatuh_tempo) {
            return 0;
        }
        $akhir = $this->tgl_kembali ?? today();
        $telat = (int) $this->tgl_jatuh_tempo->diffInDays($akhir, false);

        return $telat > 0 ? $telat * (int) config('perpustakaan.denda_per_hari') : 0;
    }

    public function label(): string
    {
        return match ($this->status) {
            'diajukan'         => 'Menunggu persetujuan',
            'dipinjam'         => 'Sedang dipinjam',
            'menunggu_kembali' => 'Menunggu konfirmasi pengembalian',
            'dikembalikan'     => 'Sudah dikembalikan',
            'ditolak'          => 'Ditolak',
            default            => $this->status,
        };
    }

    public function badge(): string
    {
        return match ($this->status) {
            'diajukan', 'menunggu_kembali' => 'warn',
            'dipinjam'                     => 'info',
            'dikembalikan'                 => 'ok',
            default                        => 'bad',
        };
    }
}
