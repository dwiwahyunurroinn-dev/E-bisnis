<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Faq extends Model
{
    protected $fillable = ['pertanyaan', 'jawaban', 'kata_kunci', 'kategori', 'aktif', 'urutan'];

    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true);
    }

    /** @return Collection<int, string> */
    public function daftarKataKunci(): Collection
    {
        return collect(explode(',', $this->kata_kunci))
            ->map(fn ($k) => trim(mb_strtolower($k)))
            ->filter();
    }
}
