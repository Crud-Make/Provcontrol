<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LeituraStatus;
use App\Models\FechamentoFrentista;
use App\Models\Leitura;
use App\Models\Recebimento;
use Illuminate\Support\Collection;

final class FechamentoMapper
{
    public function __construct(
        private readonly DecimalCalculator $decimal,
    ) {}

    /**
     * @param  Collection<int, Leitura>  $leituras
     * @return list<array<string, mixed>>
     */
    public function leituras(Collection $leituras, int $totalConcentrador): array
    {
        return $leituras->map(function (Leitura $leitura) use ($totalConcentrador): array {
            $codigo = $leitura->bico?->combustivel?->codigo ?? 'N/A';
            $numero = str_pad((string) ($leitura->bico?->numero ?? 0), 2, '0', STR_PAD_LEFT);
            $valor = $this->decimal->toInteger($leitura->valor_total, 2);

            // Leitura de rascunho (fechamento novo ainda não preenchido): o encerrante
            // final volta vazio para o operador digitar; só o inicial vem preenchido.
            $encerranteFinal = $leitura->status === LeituraStatus::Rascunho
                ? ''
                : $this->decimal->toDecimal($this->decimal->toInteger($leitura->leitura_final, 3), 3);

            return [
                'id' => $leitura->id,
                'produto' => "{$codigo} Bico {$numero}",
                'codigo' => $codigo,
                'cor' => $leitura->bico?->combustivel?->cor ?? '#94a3b8',
                'leitura_inicial' => $this->decimal->toDecimal($this->decimal->toInteger($leitura->leitura_inicial, 3), 3),
                'leitura_final' => $encerranteFinal,
                'litros_vendidos' => $this->decimal->toDecimal($this->decimal->toInteger($leitura->litros_vendidos, 3), 3),
                'preco_litro' => $this->decimal->toDecimal($this->decimal->toInteger($leitura->preco_litro, 4), 4),
                'valor_total' => $this->decimal->toDecimal($valor, 2),
                'percentual' => $this->decimal->percentage($valor, $totalConcentrador),
            ];
        })->values()->all();
    }

    /**
     * @param  Collection<int, Recebimento>  $recebimentos
     * @return Collection<int, array<string, mixed>>
     */
    public function pagamentos(Collection $recebimentos): Collection
    {
        $totalPagamentos = $this->sumDecimal($recebimentos, 'valor', 2);

        return $recebimentos->map(function (Recebimento $recebimento) use ($totalPagamentos): array {
            $taxaPercentual = $recebimento->formaPagamento?->taxa_percentual;
            $taxa = $taxaPercentual !== null
                ? $this->decimal->toInteger($taxaPercentual, 2)
                : null;
            $valor = $this->decimal->toInteger($recebimento->valor, 2);
            $despesa = $taxa !== null
                ? $this->decimal->roundDivide($valor * $taxa, 10_000)
                : 0;

            return [
                'id' => $recebimento->id,
                'tipo' => $recebimento->formaPagamento?->nome ?? 'N/A',
                'valor' => $this->decimal->toDecimal($valor, 2),
                'total' => $this->decimal->toDecimal($valor, 2),
                'percentual' => $this->decimal->percentage($valor, $totalPagamentos),
                'taxa' => $taxa !== null ? $this->decimal->toDecimal($taxa, 4) : null,
                'despesa' => $this->decimal->toDecimal($despesa, 2),
            ];
        });
    }

    /**
     * @param  Collection<int, FechamentoFrentista>  $vendas
     * @return Collection<int, array<string, mixed>>
     */
    public function frentistas(Collection $vendas, int $totalFrentistas): Collection
    {
        return $vendas->map(function (FechamentoFrentista $venda) use ($totalFrentistas): array {
            $total = $this->decimal->toInteger($venda->total_informado, 2);
            $valorConferido = $this->decimal->toInteger($venda->valor_conferido, 2);
            $diferenca = $total - $valorConferido;

            return [
                'id' => $venda->id,
                'nome' => $venda->frentista?->nome ?? 'N/A',
                'pix' => $this->decimal->money($venda->valor_pix),
                'cartao_credito' => $this->decimal->money($venda->valor_cartao_credito),
                'cartao_debito' => $this->decimal->money($venda->valor_cartao_debito),
                'moeda' => $this->decimal->money($venda->valor_moedas),
                'notas' => $this->decimal->money($venda->valor_nota),
                'baratao' => $this->decimal->money($venda->valor_baratao),
                'produtos' => $this->decimal->money($venda->valor_produtos),
                'dinheiro' => $this->decimal->money($venda->valor_dinheiro),
                'total' => $this->decimal->toDecimal($total, 2),
                'valor_conferido' => $this->decimal->toDecimal($valorConferido, 2),
                'diferenca' => $this->decimal->toDecimal($diferenca, 2),
                'percentual' => $this->decimal->percentage($total, $totalFrentistas),
            ];
        });
    }

    /**
     * @param  Collection<int, object>  $items
     */
    private function sumDecimal(Collection $items, string $field, int $scale): int
    {
        return $items->reduce(
            fn (int $total, object $item): int => $total + $this->decimal->toInteger($item->{$field}, $scale),
            0
        );
    }
}
