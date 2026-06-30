<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Voucher extends Model
{
    protected $fillable = [
        'kode', 'tipe', 'nilai', 'maks_potongan', 'min_belanja',
        'kuota', 'terpakai', 'kadaluarsa', 'aktif',
    ];

    protected $casts = [
        'nilai'         => 'decimal:2',
        'maks_potongan' => 'decimal:2',
        'min_belanja'   => 'decimal:2',
        'kadaluarsa'    => 'date',
        'aktif'         => 'boolean',
    ];

    /** Potongan untuk subtotal tertentu (0 jika tidak berlaku). */
    public function potongan(float $subtotal): int
    {
        if (! $this->berlaku($subtotal)) {
            return 0;
        }

        $potongan = $this->tipe === 'persen'
            ? $subtotal * ($this->nilai / 100)
            : (float) $this->nilai;

        if ($this->maks_potongan) {
            $potongan = min($potongan, (float) $this->maks_potongan);
        }

        return (int) min($potongan, $subtotal);
    }

    public function berlaku(float $subtotal): bool
    {
        if (! $this->aktif) {
            return false;
        }
        if ($this->kadaluarsa && $this->kadaluarsa->isPast()) {
            return false;
        }
        if (! is_null($this->kuota) && $this->terpakai >= $this->kuota) {
            return false;
        }

        return $subtotal >= (float) $this->min_belanja;
    }

    public function alasanTidakBerlaku(float $subtotal): ?string
    {
        if (! $this->aktif) {
            return 'Voucher tidak aktif.';
        }
        if ($this->kadaluarsa && $this->kadaluarsa->isPast()) {
            return 'Voucher sudah kedaluwarsa.';
        }
        if (! is_null($this->kuota) && $this->terpakai >= $this->kuota) {
            return 'Kuota voucher habis.';
        }
        if ($subtotal < (float) $this->min_belanja) {
            return 'Minimal belanja Rp'.number_format($this->min_belanja, 0, ',', '.').'.';
        }

        return null;
    }
}
