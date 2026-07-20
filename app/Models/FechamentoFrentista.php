	<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FechamentoFrentista extends Model
{
    protected $fillable = [
        'fechamento_id',
        'frentista_id',
        'valor_encerrante',
        'valor_cartao_debito',
        'valor_cartao_credito',
        'valor_cartao',
        'valor_pix',
        'valor_dinheiro',
        'valor_nota',
        'valor_moedas',
        'valor_baratao',
        'valor_produtos',
        'total_informado',
        'valor_conferido',
        'falta_caixa',
        'diferenca',
        'status',
        'observacoes',
        'enviado_por',
        'enviado_em'
    ];

    protected $casts = [
        'valor_encerrante' => 'decimal:2',
        'valor_cartao_debito' => 'decimal:2',
        'valor_cartao_credito' => 'decimal:2',
        'valor_cartao' => 'decimal:2',
        'valor_pix' => 'decimal:2',
        'valor_dinheiro' => 'decimal:2',
        'valor_nota' => 'decimal:2',
        'valor_moedas' => 'decimal:2',
        'valor_baratao' => 'decimal:2',
        'valor_produtos' => 'decimal:2',
        'total_informado' => 'decimal:2',
        'valor_conferido' => 'decimal:2',
        'falta_caixa' => 'decimal:2',
        'diferenca' => 'decimal:2',
        'enviado_em' => 'datetime',
    ];

    public function fechamento(): BelongsTo
    {
        return $this->belongsTo(Fechamento::class);
    }

    public function frentista(): BelongsTo
    {
        return $this->belongsTo(Frentista::class);
    }

    public function enviadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enviado_por');
    }
}
