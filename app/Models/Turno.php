<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
{
    protected $fillable = [
        'posto_id',
        'nome',
        'hora_inicio',
        'hora_fim',
        'ordem',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function posto(): BelongsTo
    {
        return $this->belongsTo(Posto::class);
    }

    public function leituras(): HasMany
    {
        return $this->hasMany(Leitura::class);
    }

    public function fechamentos(): HasMany
    {
        return $this->hasMany(Fechamento::class);
    }
}
