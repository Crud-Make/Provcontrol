<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeituraStatus;
use App\Models\Concerns\BelongsToPosto;
use App\Models\Concerns\InteractsWithScaledDecimals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leitura extends Model
{
    use BelongsToPosto, HasFactory, InteractsWithScaledDecimals;

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
        'observacoes',
    ];

    protected $casts = [
        'data' => 'date',
        'leitura_inicial' => 'decimal:3',
        'leitura_final' => 'decimal:3',
        'preco_litro' => 'decimal:4',
        'litros_vendidos' => 'decimal:3',
        'valor_total' => 'decimal:2',
        'status' => LeituraStatus::class,
    ];

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

    public function recalcular(): static
    {
        $inicial = $this->decimalToInteger($this->leitura_inicial, 3);
        $final = $this->decimalToInteger($this->leitura_final, 3);

        if ($final < $inicial) {
            throw new \InvalidArgumentException('A leitura final deve ser maior ou igual à inicial.');
        }

        $preco = $this->decimalToInteger($this->preco_litro, 4);
        $litros = $final - $inicial;
        $valor = $this->roundScaledDivision($litros * $preco, 100_000);

        $this->litros_vendidos = $this->integerToDecimal($litros, 3);
        $this->valor_total = $this->integerToDecimal($valor, 2);

        return $this;
    }
}
