<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leitura extends Model
{
    protected $fillable = [
        'posto_id',
        'bico_id',
        'turno_id',
        'data',
        'leitura_inicial',
        'leitura_final',
        'preco_litro',
        'litros_vendidos',
        'valor_total',
        'status',
        'user_id',
        'observacoes'
    ];

    protected $casts = [
        'data' => 'date',
        'leitura_inicial' => 'decimal:3',
        'leitura_final' => 'decimal:3',
        'preco_litro' => 'decimal:4',
        'litros_vendidos' => 'decimal:3',
        'valor_total' => 'decimal:2',
    ];

    public function posto(): BelongsTo
    {
        return $this->belongsTo(Posto::class);
    }

    public function bico(): BelongsTo
    {
        return $this->belongsTo(Bico::class);
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
