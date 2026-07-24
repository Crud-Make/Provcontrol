<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\PlanilhaJorroImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Popula leituras, fechamentos, vendas por frentista e recebimentos a partir da
 * planilha real do Posto Jorro (database/data/mes_01.csv), de forma fiel.
 */
class FechamentoPlanilhaSeeder extends Seeder
{
    public function __construct(
        private readonly PlanilhaJorroImporter $importer,
    ) {}

    public function run(): void
    {
        $postoId = (int) DB::table('postos')->value('id');
        $userId = (int) DB::table('users')->value('id');
        $turnoId = (int) DB::table('turnos')->where('nome', 'Dia')->value('id');

        $bicos = DB::table('bicos')->where('posto_id', $postoId)->pluck('id', 'numero');
        $frentistas = DB::table('frentistas')->where('posto_id', $postoId)->pluck('id', 'nome');
        $formas = DB::table('formas_pagamento')->where('posto_id', $postoId)->pluck('id', 'nome');

        $dias = $this->importer->importar(database_path('data/mes_01.csv'));

        DB::transaction(function () use ($dias, $postoId, $userId, $turnoId, $bicos, $frentistas, $formas): void {
            foreach ($dias as $dia) {
                $this->seedDia($dia, $postoId, $userId, $turnoId, $bicos, $frentistas, $formas);
            }
        });
    }

    /**
     * @param  array{data: string, concentrador: list<array{numero: int, inicial: string, final: string, preco: string}>, pagamentos: list<array{nome: string, valor: string}>, frentistas: array<string, array<string, string>>}  $dia
     * @param  Collection<int|string, int>  $bicos
     * @param  Collection<string, int>  $frentistas
     * @param  Collection<string, int>  $formas
     */
    private function seedDia(array $dia, int $postoId, int $userId, int $turnoId, $bicos, $frentistas, $formas): void
    {
        $totalConcentrador = 0.0;

        foreach ($dia['concentrador'] as $linha) {
            $bicoId = $bicos[$linha['numero']] ?? null;

            if ($bicoId === null) {
                continue;
            }

            $litros = round((float) $linha['final'] - (float) $linha['inicial'], 3);
            $valor = round($litros * (float) $linha['preco'], 2);
            $totalConcentrador += $valor;

            DB::table('leituras')->insert([
                'posto_id' => $postoId,
                'bico_id' => $bicoId,
                'turno_id' => $turnoId,
                'data' => $dia['data'],
                'leitura_inicial' => $linha['inicial'],
                'leitura_final' => $linha['final'],
                'preco_litro' => $linha['preco'],
                'litros_vendidos' => number_format($litros, 3, '.', ''),
                'valor_total' => number_format($valor, 2, '.', ''),
                'status' => 'confirmado',
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $totalConferido = 0.0;

        foreach ($dia['frentistas'] as $nome => $valores) {
            $totalConferido += (float) $valores['conferido'];
        }

        $totalConcentrador = round($totalConcentrador, 2);
        $totalConferido = round($totalConferido, 2);
        $diferenca = round($totalConcentrador - $totalConferido, 2);

        $fechamentoId = DB::table('fechamentos')->insertGetId([
            'posto_id' => $postoId,
            'turno_id' => $turnoId,
            'data' => $dia['data'],
            'status' => $diferenca === 0.0 ? 'fechado' : 'divergente',
            'total_vendas_bombas' => number_format($totalConcentrador, 2, '.', ''),
            'total_recebido' => number_format($totalConferido, 2, '.', ''),
            'diferenca' => number_format($diferenca, 2, '.', ''),
            'observacoes' => 'Importado da planilha Posto Jorro — '.$dia['data'],
            'fechado_por' => $userId,
            'fechado_em' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($dia['frentistas'] as $nome => $valores) {
            $frentistaId = $frentistas[$nome] ?? null;

            if ($frentistaId === null) {
                continue;
            }

            $cartao = round((float) $valores['cartao_credito'] + (float) $valores['cartao_debito'], 2);
            $informado = (float) $valores['informado'];
            $conferido = (float) $valores['conferido'];
            $difFrentista = round($informado - $conferido, 2);

            DB::table('fechamento_frentistas')->insert([
                'fechamento_id' => $fechamentoId,
                'frentista_id' => $frentistaId,
                'valor_cartao_debito' => $valores['cartao_debito'],
                'valor_cartao_credito' => $valores['cartao_credito'],
                'valor_cartao' => number_format($cartao, 2, '.', ''),
                'valor_pix' => $valores['pix'],
                'valor_dinheiro' => $valores['dinheiro'],
                'valor_nota' => $valores['notas'],
                'valor_moedas' => $valores['moeda'],
                'valor_baratao' => $valores['baratao'],
                'valor_produtos' => '0.00',
                'total_informado' => $valores['informado'],
                'valor_conferido' => $valores['conferido'],
                'falta_caixa' => number_format($difFrentista, 2, '.', ''),
                'diferenca' => number_format($difFrentista, 2, '.', ''),
                'status' => $difFrentista === 0.0 ? 'ok' : 'divergente',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($dia['pagamentos'] as $pagamento) {
            $formaId = $formas[$pagamento['nome']] ?? null;

            if ($formaId === null) {
                continue;
            }

            DB::table('recebimentos')->insert([
                'fechamento_id' => $fechamentoId,
                'forma_pagamento_id' => $formaId,
                'valor' => $pagamento['valor'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
