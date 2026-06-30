<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alamat extends Model
{
    protected $table = 'alamat';

    protected $fillable = [
        'user_id', 'label', 'penerima', 'telepon', 'provinsi',
        'kota', 'kota_id', 'kecamatan', 'alamat_lengkap', 'kode_pos', 'utama',
    ];

    protected $casts = ['utama' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Jadikan alamat ini sebagai utama (dan reset yang lain). */
    public function jadikanUtama(): void
    {
        static::where('user_id', $this->user_id)->where('id', '!=', $this->id)->update(['utama' => false]);
        $this->update(['utama' => true]);
    }
}
