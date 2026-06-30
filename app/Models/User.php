<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'telepon', 'aktif'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    /* ---------------- Loyalitas (standar marketplace) ---------------- */

    /** Total belanja dari pesanan yang sudah dibayar. */
    public function totalBelanja(): float
    {
        return (float) $this->pesanan()
            ->whereIn('status', ['lunas', 'diproses', 'dikirim', 'selesai'])
            ->sum('total');
    }

    /** Poin loyalitas: 1 poin per Rp10.000 belanja. */
    public function poin(): int
    {
        return (int) floor($this->totalBelanja() / 10000);
    }

    /** Tier membership berdasarkan total belanja. */
    public function tier(): string
    {
        $t = $this->totalBelanja();

        return match (true) {
            $t >= 10000000 => 'Gold',
            $t >= 3000000  => 'Silver',
            default        => 'Bronze',
        };
    }
}
