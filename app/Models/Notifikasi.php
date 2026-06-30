<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $fillable = ['user_id', 'tipe', 'judul', 'pesan', 'tautan', 'dibaca_at'];

    protected $casts = ['dibaca_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBelumDibaca(Builder $q): Builder
    {
        return $q->whereNull('dibaca_at');
    }

    public function belumDibaca(): bool
    {
        return is_null($this->dibaca_at);
    }

    /** Kirim notifikasi ke satu user. */
    public static function kirim(int $userId, string $judul, ?string $pesan = null, ?string $tautan = null, string $tipe = 'info'): void
    {
        static::create([
            'user_id' => $userId,
            'tipe'    => $tipe,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tautan'  => $tautan,
        ]);
    }

    /** Kirim ke semua admin. */
    public static function keSemuaAdmin(string $judul, ?string $pesan = null, ?string $tautan = null, string $tipe = 'info'): void
    {
        User::where('role', 'admin')->pluck('id')->each(
            fn ($id) => static::kirim($id, $judul, $pesan, $tautan, $tipe)
        );
    }
}
