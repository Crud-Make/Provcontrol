<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormaPagamento extends Model
{
    protected $fillable = [
        'posto_id',
        'nome',
        'tipo',
        'taxa_percentual',
        'ativo'
    ];

    protected $casts = [
        'taxa_percentual' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function posto(): BelongsTo
    {
        return $this->belongsTo(Posto::class);
    }

    public function recebimentos(): HasMany
    {
        return $this->hasMany(Recebimento::class);
    }
}
