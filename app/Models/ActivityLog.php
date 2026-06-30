<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'aksi', 'subjek', 'deskripsi', 'ip'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Helper pencatat aktivitas. */
    public static function catat(string $aksi, ?string $subjek = null, ?string $deskripsi = null): void
    {
        static::create([
            'user_id'   => auth()->id(),
            'aksi'      => $aksi,
            'subjek'    => $subjek,
            'deskripsi' => $deskripsi,
            'ip'        => request()->ip(),
        ]);
    }
}
