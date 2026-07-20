<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fechamento extends Model
{
    protected $fillable = [
        'posto_id',
        'turno_id',
        'data',
        'status',
        'total_vendas_bombas',
        'total_recebido',
        'diferenca',
        'observacoes',
        'fechado_por',
        'fechado_em'
    ];

    protected $casts = [
        'data' => 'date',
        'total_vendas_bombas' => 'decimal:2',
        'total_recebido' => 'decimal:2',
        'diferenca' => 'decimal:2',
        'fechado_em' => 'datetime',
    ];

    public function posto(): BelongsTo
    {
        return $this->belongsTo(Posto::class);
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    public function fechadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fechado_por');
    }

    public function fechamentoFrentistas(): HasMany
    {
        return $this->hasMany(FechamentoFrentista::class);
    }

    public function recebimentos(): HasMany
    {
        return $this->hasMany(Recebimento::class);
    }
}
