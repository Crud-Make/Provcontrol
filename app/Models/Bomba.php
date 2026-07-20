<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bomba extends Model
{
    protected $fillable = [
        'posto_id',
        'nome',
        'localizacao',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function posto(): BelongsTo
    {
        return $this->belongsTo(Posto::class);
    }

    public function bicos(): HasMany
    {
        return $this->hasMany(Bico::class);
    }
}
