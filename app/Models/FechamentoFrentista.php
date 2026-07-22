<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FechamentoFrentistaStatus;
use App\Models\Concerns\InteractsWithScaledDecimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FechamentoFrentista extends Model
{
    use InteractsWithScaledDecimals;

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
        'enviado_em',
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
        'status' => FechamentoFrentistaStatus::class,
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

    /**
     * @param  array{
     *     pix?: numeric-string,
     *     cartao_credito?: numeric-string,
     *     cartao_debito?: numeric-string,
     *     moeda?: numeric-string,
     *     notas?: numeric-string,
     *     baratao?: numeric-string,
     *     produtos?: numeric-string,
     *     dinheiro?: numeric-string,
     *     valor_conferido: numeric-string
     * }  $valores
     */
    public function atualizarValores(array $valores): static
    {
        $pix = $this->decimalToInteger($valores['pix'] ?? 0, 2);
        $credito = $this->decimalToInteger($valores['cartao_credito'] ?? 0, 2);
        $debito = $this->decimalToInteger($valores['cartao_debito'] ?? 0, 2);
        $moeda = $this->decimalToInteger($valores['moeda'] ?? 0, 2);
        $notas = $this->decimalToInteger($valores['notas'] ?? 0, 2);
        $baratao = $this->decimalToInteger($valores['baratao'] ?? 0, 2);
        $produtos = $this->decimalToInteger($valores['produtos'] ?? 0, 2);
        $dinheiro = $this->decimalToInteger($valores['dinheiro'] ?? 0, 2);
        $valorConferido = $this->decimalToInteger($valores['valor_conferido'], 2);
        $cartao = $credito + $debito;
        $total = $pix + $cartao + $moeda + $notas + $baratao + $produtos + $dinheiro;
        $diferenca = $total - $valorConferido;

        $this->fill([
            'valor_pix' => $this->integerToDecimal($pix, 2),
            'valor_cartao_credito' => $this->integerToDecimal($credito, 2),
            'valor_cartao_debito' => $this->integerToDecimal($debito, 2),
            'valor_cartao' => $this->integerToDecimal($cartao, 2),
            'valor_moedas' => $this->integerToDecimal($moeda, 2),
            'valor_nota' => $this->integerToDecimal($notas, 2),
            'valor_baratao' => $this->integerToDecimal($baratao, 2),
            'valor_produtos' => $this->integerToDecimal($produtos, 2),
            'valor_dinheiro' => $this->integerToDecimal($dinheiro, 2),
            'total_informado' => $this->integerToDecimal($total, 2),
            'valor_conferido' => $this->integerToDecimal($valorConferido, 2),
            'falta_caixa' => $this->integerToDecimal($diferenca, 2),
            'diferenca' => $this->integerToDecimal($diferenca, 2),
            'status' => $diferenca === 0
                ? FechamentoFrentistaStatus::Ok
                : FechamentoFrentistaStatus::Divergente,
        ]);

        return $this;
    }
}
