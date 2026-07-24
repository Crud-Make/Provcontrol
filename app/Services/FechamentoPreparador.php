<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FechamentoStatus;
use App\Enums\LeituraStatus;
use App\Models\Bico;
use App\Models\Fechamento;
use App\Models\FechamentoFrentista;
use App\Models\FormaPagamento;
use App\Models\Frentista;
use App\Models\Leitura;
use App\Models\Recebimento;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FechamentoPreparador
{
    /**
     * Garante que existam as linhas de rascunho de um fechamento novo para o
     * dia/turno informado: uma leitura por bico ativo (com encerrante inicial
     * pré-preenchido pela última leitura conhecida), uma venda por frentista
     * ativo e um recebimento por forma de pagamento ativa. Idempotente.
     */
    public function preparar(string $data, int $turnoId, int $postoId, User $user): void
    {
        DB::transaction(function () use ($data, $turnoId, $postoId, $user): void {
            $fechamento = $this->garantirFechamento($data, $turnoId, $postoId);
            $this->garantirLeituras($data, $turnoId, $postoId, $user);
            $this->garantirVendasFrentistas($fechamento, $postoId);
            $this->garantirRecebimentos($fechamento, $postoId);
        });
    }

    private function garantirFechamento(string $data, int $turnoId, int $postoId): Fechamento
    {
        return Fechamento::query()->firstOrCreate(
            [
                'posto_id' => $postoId,
                'turno_id' => $turnoId,
                'data' => $data,
            ],
            [
                'status' => FechamentoStatus::Aberto->value,
            ]
        );
    }

    private function garantirLeituras(string $data, int $turnoId, int $postoId, User $user): void
    {
        $bicos = Bico::query()
            ->with('combustivel')
            ->forPosto($postoId)
            ->where('ativo', true)
            ->orderBy('numero')
            ->get();

        foreach ($bicos as $bico) {
            $inicial = $this->ultimaLeituraFinal($bico->id, $postoId, $data);

            Leitura::query()->firstOrCreate(
                [
                    'posto_id' => $postoId,
                    'bico_id' => $bico->id,
                    'turno_id' => $turnoId,
                    'data' => $data,
                ],
                [
                    'leitura_inicial' => $inicial,
                    'leitura_final' => $inicial,
                    'preco_litro' => $bico->combustivel?->preco_atual ?? '0.0000',
                    'litros_vendidos' => '0.000',
                    'valor_total' => '0.00',
                    'status' => LeituraStatus::Rascunho->value,
                    'user_id' => $user->id,
                ]
            );
        }
    }

    private function ultimaLeituraFinal(int $bicoId, int $postoId, string $data): string
    {
        $final = Leitura::query()
            ->forPosto($postoId)
            ->where('bico_id', $bicoId)
            ->where('data', '<', $data)
            ->orderByDesc('data')
            ->value('leitura_final');

        return $final !== null ? (string) $final : '0.000';
    }

    private function garantirVendasFrentistas(Fechamento $fechamento, int $postoId): void
    {
        $frentistas = Frentista::query()
            ->forPosto($postoId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        foreach ($frentistas as $frentista) {
            FechamentoFrentista::query()->firstOrCreate([
                'fechamento_id' => $fechamento->id,
                'frentista_id' => $frentista->id,
            ]);
        }
    }

    private function garantirRecebimentos(Fechamento $fechamento, int $postoId): void
    {
        $formas = FormaPagamento::query()
            ->forPosto($postoId)
            ->where('ativo', true)
            ->orderBy('id')
            ->get();

        foreach ($formas as $forma) {
            Recebimento::query()->firstOrCreate(
                [
                    'fechamento_id' => $fechamento->id,
                    'forma_pagamento_id' => $forma->id,
                ],
                [
                    'valor' => '0.00',
                ]
            );
        }
    }
}
