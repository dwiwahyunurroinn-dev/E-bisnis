<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObrolanPesan extends Model
{
    protected $fillable = ['obrolan_id', 'pengirim', 'pesan'];

    public function obrolan(): BelongsTo
    {
        return $this->belongsTo(Obrolan::class);
    }
}
