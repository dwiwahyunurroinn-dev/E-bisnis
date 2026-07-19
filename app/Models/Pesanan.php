<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'kode', 'user_id', 'alamat_id',
        'penerima', 'telepon', 'kota', 'alamat_lengkap', 'kode_pos',
        'kurir', 'layanan', 'resi',
        'subtotal', 'diskon', 'kode_voucher', 'ongkir', 'total', 'metode_bayar', 'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'diskon'   => 'decimal:2',
        'ongkir'   => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alamat(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'alamat_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }

    /* ---------------- Metode pembayaran ---------------- */

    /** Info kanal pembayaran dari config/pembayaran.php. */
    public function kanalBayar(): ?array
    {
        return config('pembayaran.kanal.'.$this->metode_bayar);
    }

    public function labelMetode(): ?string
    {
        return $this->kanalBayar()['label']
            ?? ($this->metode_bayar ? ucfirst($this->metode_bayar) : null);
    }

    public function isCod(): bool
    {
        return $this->metode_bayar === 'cod';
    }

    /** Nomor Virtual Account deterministik dari kode pesanan (mode simulasi). */
    public function nomorVa(): ?string
    {
        $kanal = $this->kanalBayar();
        if (($kanal['tipe'] ?? null) !== 'va') {
            return null;
        }

        return $kanal['prefix'].str_pad((string) (crc32($this->kode) % 10000000000), 10, '0', STR_PAD_LEFT);
    }
}
