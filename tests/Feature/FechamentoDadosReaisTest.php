<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use App\Services\FechamentoService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FechamentoDadosReaisTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_retorna_dados_reais_do_dia_01(): void
    {
        $this->seedAndAuthenticate();

        $turnoId = Turno::query()->where('nome', 'Dia')->value('id');

        $response = $this->postJson(route('fechamentos.preview'), [
            'data' => '2025-01-01',
            'turno_id' => $turnoId,
        ]);

        $response->assertOk()
            ->assertJsonPath('total_concentrador', '8203.87')
            ->assertJsonPath('total_informado_frentistas', '8215.09')
            ->assertJsonPath('total_frentistas', '8219.76')
            ->assertJsonPath('diferenca', '-15.89')
            ->assertJsonPath('total_pagamentos', '4859.00')
            ->assertJsonPath('total_taxas', '43.11')
            ->assertJsonCount(6, 'leituras')
            ->assertJsonCount(8, 'frentistas')
            ->assertJsonCount(5, 'pagamentos');
    }

    public function test_service_calcula_fechamento_com_detalhes(): void
    {
        $this->seedAndAuthenticate();

        $turnoId = (int) Turno::query()->where('nome', 'Dia')->value('id');
        $postoId = (int) Posto::query()->value('id');
        $resultado = app(FechamentoService::class)->calcularFechamento('2025-01-01', $turnoId, $postoId);

        $this->assertSame('8203.87', $resultado['total_concentrador']);
        $this->assertSame('8215.09', $resultado['total_informado_frentistas']);
        $this->assertSame('8219.76', $resultado['total_frentistas']);
        $this->assertSame('-15.89', $resultado['diferenca']);
        $this->assertSame('GC Bico 01', $resultado['leituras'][0]['produto']);
        $this->assertSame('Leandro', $resultado['frentistas'][0]['nome']);
        $this->assertSame('2839.08', $resultado['frentistas'][0]['total']);
        $this->assertSame('2839.08', $resultado['frentistas'][0]['valor_conferido']);
    }

    public function test_store_fecha_mesmo_com_divergencia(): void
    {
        $this->seedAndAuthenticate();

        $turnoId = Turno::query()->where('nome', 'Dia')->value('id');

        $response = $this->post(route('fechamentos.store'), [
            'data' => '2025-01-01',
            'turno_id' => $turnoId,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('fechamentos', [
            'data' => '2025-01-01',
            'turno_id' => $turnoId,
            'total_vendas_bombas' => '8203.87',
            'total_recebido' => '8219.76',
            'diferenca' => '-15.89',
            'status' => 'divergente',
        ]);
    }

    public function test_atualizar_recalcula_totais_no_servidor(): void
    {
        $this->seedAndAuthenticate();

        $turnoId = Turno::query()->where('nome', 'Dia')->value('id');
        $preview = $this->postJson(route('fechamentos.preview'), [
            'data' => '2025-01-01',
            'turno_id' => $turnoId,
        ])->json();

        $leitura = $preview['leituras'][0];
        $leitura['preco_litro'] = '6.4800';

        $response = $this->postJson(route('fechamentos.atualizar'), [
            'data' => '2025-01-01',
            'turno_id' => $turnoId,
            'leituras' => [[
                'id' => $leitura['id'],
                'leitura_inicial' => $leitura['leitura_inicial'],
                'leitura_final' => $leitura['leitura_final'],
                'preco_litro' => $leitura['preco_litro'],
            ]],
            'frentistas' => collect($preview['frentistas'])->map(fn (array $f): array => [
                'id' => $f['id'],
                'pix' => $f['pix'],
                'cartao_credito' => $f['cartao_credito'],
                'cartao_debito' => $f['cartao_debito'],
                'moeda' => $f['moeda'],
                'notas' => $f['notas'],
                'baratao' => $f['baratao'],
                'produtos' => $f['produtos'],
                'dinheiro' => $f['dinheiro'],
                'valor_conferido' => $f['valor_conferido'],
            ])->all(),
            'pagamentos' => collect($preview['pagamentos'])->map(fn (array $p): array => [
                'id' => $p['id'],
                'valor' => $p['valor'],
            ])->all(),
        ]);

        $response->assertOk();
        $response->assertJsonPath('total_concentrador', '8263.25');
        $this->assertArrayHasKey('valor', $response->json('pagamentos.0'));
        $this->assertArrayNotHasKey('inter_pog', $response->json('pagamentos.0'));
    }

    public function test_total_conferido_preserva_falta_informada_na_planilha(): void
    {
        $this->seedAndAuthenticate();

        $turnoId = Turno::query()->where('nome', 'Dia')->value('id');
        $preview = $this->postJson(route('fechamentos.preview'), [
            'data' => '2025-01-01',
            'turno_id' => $turnoId,
        ])->json();

        $gabi = collect($preview['frentistas'])->firstWhere('nome', 'Gabi');

        $response = $this->postJson(route('fechamentos.atualizar'), [
            'data' => '2025-01-01',
            'turno_id' => $turnoId,
            'frentistas' => [[
                'id' => $gabi['id'],
                'pix' => $gabi['pix'],
                'cartao_credito' => $gabi['cartao_credito'],
                'cartao_debito' => $gabi['cartao_debito'],
                'moeda' => $gabi['moeda'],
                'notas' => $gabi['notas'],
                'baratao' => $gabi['baratao'],
                'produtos' => $gabi['produtos'],
                'dinheiro' => $gabi['dinheiro'],
                'valor_conferido' => $gabi['valor_conferido'],
            ]],
        ]);

        $response->assertOk()
            ->assertJsonPath('total_informado_frentistas', '8215.09')
            ->assertJsonPath('total_frentistas', '8219.76')
            ->assertJsonPath('diferenca', '-15.89')
            ->assertJsonPath('frentistas.2.diferenca', '-4.67');
    }

    public function test_calculo_considera_apenas_leituras_do_turno_solicitado(): void
    {
        $this->seedAndAuthenticate();

        $postoId = (int) Posto::query()->value('id');
        $turnoDia = Turno::query()->where('nome', 'Dia')->firstOrFail();
        $turnoNoiteId = DB::table('turnos')->insertGetId([
            'posto_id' => $postoId,
            'nome' => 'Noite',
            'hora_inicio' => '18:00',
            'hora_fim' => '06:00',
            'ordem' => 2,
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('leituras')->insert([
            'posto_id' => $postoId,
            'bico_id' => DB::table('bicos')->where('numero', 1)->value('id'),
            'turno_id' => $turnoNoiteId,
            'data' => '2025-01-01',
            'leitura_inicial' => '100.000',
            'leitura_final' => '110.000',
            'preco_litro' => '6.3800',
            'litros_vendidos' => '10.000',
            'valor_total' => '63.80',
            'status' => 'confirmado',
            'user_id' => DB::table('users')->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $resultado = app(FechamentoService::class)->calcularFechamento(
            '2025-01-01',
            $turnoDia->id,
            $postoId
        );

        $this->assertSame('8203.87', $resultado['total_concentrador']);
        $this->assertCount(6, $resultado['leituras']);
    }

    private function seedAndAuthenticate(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->firstOrFail();
        $posto = Posto::query()->firstOrFail();

        $this->actingAs($user)->withSession(['posto_id' => $posto->id]);
    }
}
