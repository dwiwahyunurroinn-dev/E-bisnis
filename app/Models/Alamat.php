<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alamat extends Model
{
    protected $table = 'alamat';

    protected $fillable = [
        'user_id', 'label', 'penerima', 'telepon', 'provinsi',
        'kota', 'kota_id', 'kecamatan', 'alamat_lengkap', 'kode_pos',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
