<?php

namespace App\Services;

use App\Models\Leitura;
use App\Models\Fechamento;
use App\Models\FechamentoFrentista;
use App\Models\Recebimento;
use Illuminate\Support\Facades\DB;

class FechamentoService
{
    public function calcularFechamento(string $data, int $turnoId): array
    {
        $postoId = 1; // Posto Providência

        // 1. Calcular total do concentrador
        $totalConcentrador = Leitura::where('data', $data)
            ->where('posto_id', $postoId)
            ->sum('valor_total');

        // 2. Calcular total dos frentistas
        $fechamento = Fechamento::where('data', $data)
            ->where('turno_id', $turnoId)
            ->where('posto_id', $postoId)
            ->first();

        $totalFrentistas = 0;
        if ($fechamento) {
            $totalFrentistas = FechamentoFrentista::where('fechamento_id', $fechamento->id)
                ->sum('total_informado');
        }

        // 3. Calcular total de recebimentos
        $totalRecebimentos = 0;
        if ($fechamento) {
            $totalRecebimentos = Recebimento::where('fechamento_id', $fechamento->id)
                ->sum('valor');
        }

        // 4. Calcular diferença
        $totalRecebido = $totalFrentistas + $totalRecebimentos;
        $diferenca = $totalConcentrador - $totalRecebido;

        return [
            'data' => $data,
            'turno_id' => $turnoId,
            'total_concentrador' => round($totalConcentrador, 2),
            'total_frentistas' => round($totalFrentistas, 2),
            'total_recebimentos' => round($totalRecebimentos, 2),
            'total_recebido' => round($totalRecebido, 2),
            'diferenca' => round($diferenca, 2),
            'status' => $diferenca == 0 ? 'fechado' : 'divergente',
        ];
    }

    public function fechar(array $dados): Fechamento
    {
        $fechamento = Fechamento::create([
            'posto_id' => 1,
            'turno_id' => $dados['turno_id'],
            'data' => $dados['data'],
            'status' => $dados['status'],
            'total_vendas_bombas' => $dados['total_concentrador'],
            'total_recebido' => $dados['total_recebido'],
            'diferenca' => $dados['diferenca'],
            'observacoes' => $dados['observacoes'] ?? null,
            'fechado_por' => auth()->id(),
            'fechado_em' => now(),
        ]);

        return $fechamento;
    }
}
