<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FechamentoNovoTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_de_dia_novo_gera_linhas_de_encerrante_por_bico(): void
    {
        $this->seedAndAuthenticate();
        $turnoId = (int) Turno::query()->where('nome', 'Dia')->value('id');

        $response = $this->postJson(route('fechamentos.preview'), [
            'data' => '2025-06-15',
            'turno_id' => $turnoId,
        ]);

        $response->assertOk()
            ->assertJsonCount(6, 'leituras')
            ->assertJsonCount(7, 'frentistas')
            ->assertJsonCount(5, 'pagamentos')
            ->assertJsonPath('total_concentrador', '0.00')
            ->assertJsonPath('total_frentistas', '0.00');

        // Encerrante final vem vazio em todas as leituras do rascunho.
        foreach ($response->json('leituras') as $leitura) {
            $this->assertSame('', $leitura['leitura_final']);
            $this->assertNotSame('', $leitura['leitura_inicial']);
        }

        $this->assertDatabaseHas('fechamentos', [
            'data' => '2025-06-15',
            'turno_id' => $turnoId,
        ]);
        $this->assertDatabaseHas('leituras', [
            'data' => '2025-06-15',
            'turno_id' => $turnoId,
            'status' => 'rascunho',
        ]);
    }

    public function test_encerrante_inicial_vem_da_ultima_leitura_conhecida(): void
    {
        $this->seedAndAuthenticate();
        $turnoId = (int) Turno::query()->where('nome', 'Dia')->value('id');

        $response = $this->postJson(route('fechamentos.preview'), [
            'data' => '2025-06-15',
            'turno_id' => $turnoId,
        ]);

        // Bico 01 encerrou em 1734434.002 na última leitura conhecida do mês 01
        // (FechamentoPlanilhaSeeder importa o mês inteiro; a última data anterior a 2025-06-15).
        $bico01 = collect($response->json('leituras'))
            ->firstWhere('produto', 'GC Bico 01');

        $this->assertSame('1734434.002', $bico01['leitura_inicial']);
        $this->assertSame('', $bico01['leitura_final']);
        $this->assertSame('0.000', $bico01['litros_vendidos']);
    }

    public function test_preview_repetido_e_idempotente(): void
    {
        $this->seedAndAuthenticate();
        $turnoId = (int) Turno::query()->where('nome', 'Dia')->value('id');

        $payload = ['data' => '2025-06-15', 'turno_id' => $turnoId];

        $this->postJson(route('fechamentos.preview'), $payload)->assertOk();
        $this->postJson(route('fechamentos.preview'), $payload)->assertOk();

        $this->assertDatabaseCount('leituras', 150); // 144 do mês (24 dias × 6 bicos) + 6 do novo dia
    }

    public function test_usuario_informa_encerrante_e_total_recalcula_no_servidor(): void
    {
        $this->seedAndAuthenticate();
        $turnoId = (int) Turno::query()->where('nome', 'Dia')->value('id');

        $preview = $this->postJson(route('fechamentos.preview'), [
            'data' => '2025-06-15',
            'turno_id' => $turnoId,
        ])->json();

        $bico01 = collect($preview['leituras'])->firstWhere('produto', 'GC Bico 01');
        $inicial = (float) $bico01['leitura_inicial'];

        $response = $this->postJson(route('fechamentos.atualizar'), [
            'data' => '2025-06-15',
            'turno_id' => $turnoId,
            'leituras' => [[
                'id' => $bico01['id'],
                'leitura_inicial' => $bico01['leitura_inicial'],
                'leitura_final' => number_format($inicial + 10, 3, '.', ''),
                'preco_litro' => '6.0000',
            ]],
        ]);

        $response->assertOk()
            ->assertJsonPath('total_concentrador', '60.00');

        // Após informar o encerrante, a leitura deixa de ser rascunho e passa a exibir o final.
        $bico01Salvo = collect($response->json('leituras'))->firstWhere('produto', 'GC Bico 01');
        $this->assertSame(number_format($inicial + 10, 3, '.', ''), $bico01Salvo['leitura_final']);

        $this->assertDatabaseHas('leituras', [
            'id' => $bico01['id'],
            'status' => 'confirmado',
        ]);
    }

    private function seedAndAuthenticate(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->firstOrFail();
        $posto = Posto::query()->firstOrFail();

        $this->actingAs($user)->withSession(['posto_id' => $posto->id]);
    }
}
