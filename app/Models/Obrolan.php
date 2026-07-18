<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obrolan extends Model
{
    protected $fillable = ['user_id', 'token_tamu', 'nama', 'status', 'admin_id', 'terakhir_pesan_at'];

    protected $casts = ['terakhir_pesan_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function pesan(): HasMany
    {
        return $this->hasMany(ObrolanPesan::class);
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('status', '!=', 'selesai');
    }

    public function scopeMenungguAdmin(Builder $q): Builder
    {
        return $q->where('status', 'menunggu_admin');
    }

    public function namaTampil(): string
    {
        return $this->nama ?: ($this->user->name ?? 'Tamu');
    }
}
