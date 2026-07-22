<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FechamentoDia01Seeder extends Seeder
{
    /**
     * Fechamento Dia 01 — vendas frentistas + pagamentos eletrônicos (planilha).
     */
    public function run(): void
    {
        $postoId = (int) DB::table('postos')->first()->id;
        $turnoId = (int) DB::table('turnos')->where('nome', 'Dia')->value('id');
        $data = '2025-01-01';

        $fechamentoId = DB::table('fechamentos')->insertGetId([
            'posto_id' => $postoId,
            'turno_id' => $turnoId,
            'data' => $data,
            'status' => 'aberto',
            'total_vendas_bombas' => '8203.87',
            'total_recebido' => '8219.76',
            'diferenca' => '-15.89',
            'observacoes' => 'Dados reais da planilha Posto Jorro — Dia 01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->seedVendasFrentistas($fechamentoId);
        $this->seedPagamentosEletronicos($fechamentoId, $postoId);
    }

    private function seedVendasFrentistas(int $fechamentoId): void
    {
        $vendas = [
            'Leandro' => [
                'pix' => '0.00',
                'cartao_credito' => '2346.00',
                'cartao_debito' => '0.00',
                'cartao' => '2346.00',
                'moedas' => '0.00',
                'nota' => '50.00',
                'baratao' => '0.00',
                'dinheiro' => '443.08',
                'total' => '2839.08',
                'conferido' => '2839.08',
            ],
            'Paulo' => [
                'pix' => '0.00',
                'cartao_credito' => '0.00',
                'cartao_debito' => '0.00',
                'cartao' => '0.00',
                'moedas' => '0.00',
                'nota' => '0.00',
                'baratao' => '0.00',
                'dinheiro' => '0.00',
                'total' => '0.00',
                'conferido' => '0.00',
            ],
            'Gabi' => [
                'pix' => '0.00',
                'cartao_credito' => '1225.00',
                'cartao_debito' => '0.00',
                'cartao' => '1225.00',
                'moedas' => '0.00',
                'nota' => '280.00',
                'baratao' => '0.00',
                'dinheiro' => '775.00',
                'total' => '2280.00',
                'conferido' => '2284.67',
            ],
            'Eliane' => [
                'pix' => '370.00',
                'cartao_credito' => '190.00',
                'cartao_debito' => '0.00',
                'cartao' => '190.00',
                'moedas' => '0.00',
                'nota' => '20.00',
                'baratao' => '0.00',
                'dinheiro' => '553.70',
                'total' => '1133.70',
                'conferido' => '1133.70',
            ],
            'Sinho' => [
                'pix' => '0.00',
                'cartao_credito' => '310.00',
                'cartao_debito' => '0.00',
                'cartao' => '310.00',
                'moedas' => '0.00',
                'nota' => '0.00',
                'baratao' => '0.00',
                'dinheiro' => '171.68',
                'total' => '481.68',
                'conferido' => '481.68',
            ],
            'Nayla' => [
                'pix' => '285.00',
                'cartao_credito' => '409.00',
                'cartao_debito' => '0.00',
                'cartao' => '409.00',
                'moedas' => '0.00',
                'nota' => '100.00',
                'baratao' => '0.00',
                'dinheiro' => '686.63',
                'total' => '1480.63',
                'conferido' => '1480.63',
            ],
            'Elyon' => [
                'pix' => '0.00',
                'cartao_credito' => '0.00',
                'cartao_debito' => '0.00',
                'cartao' => '0.00',
                'moedas' => '0.00',
                'nota' => '0.00',
                'baratao' => '0.00',
                'dinheiro' => '0.00',
                'total' => '0.00',
                'conferido' => '0.00',
            ],
            'Elias' => [
                'pix' => '0.00',
                'cartao_credito' => '0.00',
                'cartao_debito' => '0.00',
                'cartao' => '0.00',
                'moedas' => '0.00',
                'nota' => '0.00',
                'baratao' => '0.00',
                'dinheiro' => '0.00',
                'total' => '0.00',
                'conferido' => '0.00',
            ],
        ];

        foreach ($vendas as $nome => $v) {
            $frentistaId = DB::table('frentistas')->where('nome', $nome)->value('id');
            $diferenca = $nome === 'Gabi' ? '-4.67' : '0.00';

            DB::table('fechamento_frentistas')->insert([
                'fechamento_id' => $fechamentoId,
                'frentista_id' => $frentistaId,
                'valor_cartao_debito' => $v['cartao_debito'],
                'valor_cartao_credito' => $v['cartao_credito'],
                'valor_cartao' => $v['cartao'],
                'valor_pix' => $v['pix'],
                'valor_dinheiro' => $v['dinheiro'],
                'valor_nota' => $v['nota'],
                'valor_moedas' => $v['moedas'],
                'valor_baratao' => $v['baratao'],
                'valor_produtos' => 0,
                'total_informado' => $v['total'],
                'valor_conferido' => $v['conferido'],
                'falta_caixa' => $diferenca,
                'diferenca' => $diferenca,
                'status' => $diferenca === '0.00' ? 'ok' : 'divergente',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedPagamentosEletronicos(int $fechamentoId, int $postoId): void
    {
        $pagamentos = [
            'Cartão C' => '1762.00',
            'Cartão B' => '1231.00',
            'Pix' => '1866.00',
            'APP Baratão' => '0.00',
            'APP Providência' => '0.00',
        ];

        foreach ($pagamentos as $nome => $valor) {
            $formaId = DB::table('formas_pagamento')
                ->where('posto_id', $postoId)
                ->where('nome', $nome)
                ->value('id');

            DB::table('recebimentos')->insert([
                'fechamento_id' => $fechamentoId,
                'forma_pagamento_id' => $formaId,
                'valor' => $valor,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
