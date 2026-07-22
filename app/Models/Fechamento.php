<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FechamentoStatus;
use App\Models\Concerns\BelongsToPosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fechamento extends Model
{
    use BelongsToPosto, HasFactory;

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
        'fechado_em',
    ];

    protected $casts = [
        'data' => 'date',
        'total_vendas_bombas' => 'decimal:2',
        'total_recebido' => 'decimal:2',
        'diferenca' => 'decimal:2',
        'status' => FechamentoStatus::class,
        'fechado_em' => 'datetime',
    ];

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

    public function isFechado(): bool
    {
        return $this->status === FechamentoStatus::Fechado;
    }

    public function isConferido(): bool
    {
        return $this->diferenca === '0.00';
    }

    public function statusLabel(): string
    {
        return ucfirst($this->status->value);
    }
}
