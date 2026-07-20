<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Combustivel extends Model
{
    protected $fillable = [
        'posto_id',
        'nome',
        'codigo',
        'cor',
        'preco_atual',
        'ativo'
    ];

    protected $casts = [
        'preco_atual' => 'decimal:4',
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
