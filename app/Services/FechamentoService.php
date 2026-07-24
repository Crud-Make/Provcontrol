<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FechamentoStatus;
use App\Enums\LeituraStatus;
use App\Models\Fechamento;
use App\Models\FechamentoFrentista;
use App\Models\Leitura;
use App\Models\Recebimento;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FechamentoService
{
    public function __construct(
        private readonly DecimalCalculator $decimal,
        private readonly FechamentoMapper $mapper,
    ) {}

    /**
     * @return array{
     *     data: string,
     *     turno_id: int,
     *     leituras: list<array<string, mixed>>,
     *     pagamentos: list<array<string, mixed>>,
     *     frentistas: list<array<string, mixed>>,
     *     total_concentrador: string,
     *     total_litros: string,
     *     total_frentistas: string,
     *     total_informado_frentistas: string,
     *     total_pagamentos: string,
     *     total_taxas: string,
     *     total_pix_frentistas: string,
     *     total_cartao_credito_frentistas: string,
     *     total_cartao_debito_frentistas: string,
     *     total_moeda_frentistas: string,
     *     total_notas_frentistas: string,
     *     total_baratao_frentistas: string,
     *     total_produtos_frentistas: string,
     *     total_dinheiro_frentistas: string,
     *     concentrador_x_frentista: string,
     *     diferenca: string,
     *     status: string
     * }
     */
    public function calcularFechamento(string $data, int $turnoId, int $postoId): array
    {
        $leituras = Leitura::query()
            ->with(['bico.combustivel'])
            ->where('posto_id', $postoId)
            ->where('data', $data)
            ->where('turno_id', $turnoId)
            ->orderBy('bico_id')
            ->get();

        $totalConcentrador = $this->sumDecimal($leituras, 'valor_total', 2);
        $totalLitros = $this->sumDecimal($leituras, 'litros_vendidos', 3);

        $fechamento = Fechamento::query()
            ->where('posto_id', $postoId)
            ->where('data', $data)
            ->where('turno_id', $turnoId)
            ->first();

        $vendasFrentistas = $fechamento
            ? FechamentoFrentista::query()
                ->with('frentista')
                ->where('fechamento_id', $fechamento->id)
                ->get()
            : collect();

        $recebimentos = $fechamento
            ? Recebimento::query()
                ->with('formaPagamento')
                ->where('fechamento_id', $fechamento->id)
                ->get()
            : collect();

        $totalInformadoFrentistas = $this->sumDecimal($vendasFrentistas, 'total_informado', 2);
        $totalFrentistas = $this->sumDecimal($vendasFrentistas, 'valor_conferido', 2);
        $diferenca = $totalConcentrador - $totalFrentistas;

        $pagamentos = $this->mapper->pagamentos($recebimentos);
        $totalPagamentos = $this->sumArrayDecimal($pagamentos, 'total', 2);
        $totalTaxas = $this->sumArrayDecimal($pagamentos, 'despesa', 2);

        $frentistas = $this->mapper->frentistas($vendasFrentistas, $totalInformadoFrentistas);

        return [
            'data' => $data,
            'turno_id' => $turnoId,
            'leituras' => $this->mapper->leituras($leituras, $totalConcentrador),
            'pagamentos' => $pagamentos->values()->all(),
            'frentistas' => $frentistas->values()->all(),
            'total_concentrador' => $this->decimal->toDecimal($totalConcentrador, 2),
            'total_litros' => $this->decimal->toDecimal($totalLitros, 3),
            'total_frentistas' => $this->decimal->toDecimal($totalFrentistas, 2),
            'total_informado_frentistas' => $this->decimal->toDecimal($totalInformadoFrentistas, 2),
            'total_pagamentos' => $this->decimal->toDecimal($totalPagamentos, 2),
            'total_taxas' => $this->decimal->toDecimal($totalTaxas, 2),
            'total_pix_frentistas' => $this->decimal->toDecimal($this->sumDecimal($vendasFrentistas, 'valor_pix', 2), 2),
            'total_cartao_credito_frentistas' => $this->decimal->toDecimal($this->sumDecimal($vendasFrentistas, 'valor_cartao_credito', 2), 2),
            'total_cartao_debito_frentistas' => $this->decimal->toDecimal($this->sumDecimal($vendasFrentistas, 'valor_cartao_debito', 2), 2),
            'total_moeda_frentistas' => $this->decimal->toDecimal($this->sumDecimal($vendasFrentistas, 'valor_moedas', 2), 2),
            'total_notas_frentistas' => $this->decimal->toDecimal($this->sumDecimal($vendasFrentistas, 'valor_nota', 2), 2),
            'total_baratao_frentistas' => $this->decimal->toDecimal($this->sumDecimal($vendasFrentistas, 'valor_baratao', 2), 2),
            'total_produtos_frentistas' => $this->decimal->toDecimal($this->sumDecimal($vendasFrentistas, 'valor_produtos', 2), 2),
            'total_dinheiro_frentistas' => $this->decimal->toDecimal($this->sumDecimal($vendasFrentistas, 'valor_dinheiro', 2), 2),
            'concentrador_x_frentista' => $this->decimal->toDecimal($diferenca, 2),
            'diferenca' => $this->decimal->toDecimal($diferenca, 2),
            'status' => $diferenca === 0
                ? FechamentoStatus::Fechado->value
                : FechamentoStatus::Divergente->value,
        ];
    }

    /**
     * @param  array{
     *     turno_id: int,
     *     data: string,
     *     status: string,
     *     total_concentrador: string,
     *     total_recebido: string,
     *     diferenca: string,
     *     observacoes?: string|null,
     * }  $dados
     */
    public function fechar(array $dados, int $postoId, User $user): Fechamento
    {
        return Fechamento::query()->updateOrCreate(
            [
                'posto_id' => $postoId,
                'turno_id' => $dados['turno_id'],
                'data' => $dados['data'],
            ],
            [
                'status' => $dados['status'],
                'total_vendas_bombas' => $dados['total_concentrador'],
                'total_recebido' => $dados['total_recebido'],
                'diferenca' => $dados['diferenca'],
                'observacoes' => $dados['observacoes'] ?? null,
                'fechado_por' => $user->id,
                'fechado_em' => now(),
            ]
        );
    }

    /**
     * Persiste alterações manuais do preview (leituras, frentistas, pagamentos).
     *
     * @param  array{
     *     data: string,
     *     turno_id: int,
     *     leituras?: list<array{id: int, leitura_inicial: numeric-string, leitura_final: numeric-string, preco_litro: numeric-string}>,
     *     frentistas?: list<array{id: int, pix?: numeric-string, cartao_credito?: numeric-string, cartao_debito?: numeric-string, moeda?: numeric-string, notas?: numeric-string, baratao?: numeric-string, produtos?: numeric-string, dinheiro?: numeric-string, valor_conferido: numeric-string}>,
     *     pagamentos?: list<array{id: int, valor: numeric-string}>
     * }  $dados
     */
    public function atualizarDados(array $dados, int $postoId): void
    {
        DB::transaction(function () use ($dados, $postoId): void {
            foreach ($dados['leituras'] ?? [] as $item) {
                $leitura = Leitura::query()
                    ->whereKey($item['id'])
                    ->where('posto_id', $postoId)
                    ->where('data', $dados['data'])
                    ->where('turno_id', $dados['turno_id'])
                    ->firstOrFail();

                $leitura->fill([
                    'leitura_inicial' => $item['leitura_inicial'],
                    'leitura_final' => $item['leitura_final'],
                    'preco_litro' => $item['preco_litro'],
                    'status' => LeituraStatus::Confirmado->value,
                ])->recalcular()->save();
            }

            foreach ($dados['frentistas'] ?? [] as $item) {
                $venda = FechamentoFrentista::query()
                    ->whereKey($item['id'])
                    ->whereHas('fechamento', fn ($query) => $query
                        ->where('posto_id', $postoId)
                        ->where('data', $dados['data'])
                        ->where('turno_id', $dados['turno_id']))
                    ->firstOrFail();

                $venda->atualizarValores($item)->save();
            }

            foreach ($dados['pagamentos'] ?? [] as $item) {
                $recebimento = Recebimento::query()
                    ->whereKey($item['id'])
                    ->whereHas('fechamento', fn ($query) => $query
                        ->where('posto_id', $postoId)
                        ->where('data', $dados['data'])
                        ->where('turno_id', $dados['turno_id']))
                    ->firstOrFail();

                $recebimento->update([
                    'valor' => $this->decimal->toDecimal(
                        $this->decimal->toInteger($item['valor'], 2),
                        2
                    ),
                ]);
            }
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

    /**
     * @param  Collection<int, array<string, mixed>>  $items
     */
    private function sumArrayDecimal(Collection $items, string $field, int $scale): int
    {
        return $items->reduce(
            fn (int $total, array $item): int => $total + $this->decimal->toInteger($item[$field], $scale),
            0
        );
    }
}
