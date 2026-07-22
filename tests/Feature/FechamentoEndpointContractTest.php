<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Fechamento;
use App\Models\Leitura;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class FechamentoEndpointContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_endpoints_de_fechamento_respondem_no_fluxo_feliz(): void
    {
        ['user' => $user, 'posto' => $posto] = $this->authenticateForPosto();
        $turno = Turno::factory()->for($posto)->create();

        $this->get(route('fechamentos.index'))->assertOk();
        $this->get(route('fechamentos.create'))->assertOk();

        $this->postJson(route('fechamentos.preview'), [
            'data' => now()->toDateString(),
            'turno_id' => $turno->id,
        ])->assertOk()
            ->assertJsonPath('total_concentrador', '0.00');

        $this->postJson(route('fechamentos.atualizar'), [
            'data' => now()->toDateString(),
            'turno_id' => $turno->id,
            'leituras' => [],
            'frentistas' => [],
            'pagamentos' => [],
        ])->assertOk();

        $this->post(route('fechamentos.store'), [
            'data' => now()->toDateString(),
            'turno_id' => $turno->id,
        ])->assertRedirect();

        $fechamento = Fechamento::query()
            ->where('posto_id', $posto->id)
            ->firstOrFail();

        $this->assertSame($user->id, $fechamento->fechado_por);
        $this->get(route('fechamentos.show', $fechamento))->assertOk();
    }

    public function test_endpoints_de_escrita_rejeitam_payload_invalido(): void
    {
        $this->authenticateForPosto();

        $preview = $this->postJson(route('fechamentos.preview'), []);
        $this->assertSame(422, $preview->getStatusCode(), $preview->getContent());
        $previewErrors = $preview->json('errors');
        $this->assertIsArray($previewErrors);
        $this->assertArrayHasKey('data', $previewErrors);
        $this->assertArrayHasKey('turno_id', $previewErrors);

        $atualizacao = $this->postJson(route('fechamentos.atualizar'), []);
        $this->assertSame(422, $atualizacao->getStatusCode(), $atualizacao->getContent());
        $atualizacaoErrors = $atualizacao->json('errors');
        $this->assertIsArray($atualizacaoErrors);
        $this->assertArrayHasKey('data', $atualizacaoErrors);
        $this->assertArrayHasKey('turno_id', $atualizacaoErrors);

        $this->post(route('fechamentos.store'), [])
            ->assertSessionHasErrors(['data', 'turno_id']);
    }

    public function test_policy_rejeita_fechamento_de_outro_posto(): void
    {
        ['user' => $user] = $this->authenticateForPosto();
        $outroPosto = Posto::factory()->create();
        $turnoExterno = Turno::factory()->for($outroPosto)->create();
        $fechamentoExterno = Fechamento::factory()
            ->for($outroPosto)
            ->for($turnoExterno)
            ->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse(Gate::forUser($user)->allows('view', $fechamentoExterno));
    }

    public function test_atualizacao_rejeita_leitura_de_outro_posto(): void
    {
        ['posto' => $posto] = $this->authenticateForPosto();
        $turno = Turno::factory()->for($posto)->create();
        $outroPosto = Posto::factory()->create();
        $turnoExterno = Turno::factory()->for($outroPosto)->create();
        $leituraExterna = Leitura::factory()
            ->for($outroPosto)
            ->for($turnoExterno)
            ->create(['data' => now()->toDateString()]);

        $response = $this->postJson(route('fechamentos.atualizar'), [
            'data' => now()->toDateString(),
            'turno_id' => $turno->id,
            'leituras' => [[
                'id' => $leituraExterna->id,
                'leitura_inicial' => $leituraExterna->leitura_inicial,
                'leitura_final' => $leituraExterna->leitura_final,
                'preco_litro' => $leituraExterna->preco_litro,
            ]],
        ]);

        $this->assertSame(422, $response->getStatusCode(), $response->getContent());
        $errors = $response->json('errors');
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('leituras.0.id', $errors);
    }

    public function test_atualizacao_rejeita_leitura_final_menor_que_inicial(): void
    {
        ['posto' => $posto] = $this->authenticateForPosto();
        $turno = Turno::factory()->for($posto)->create();
        $leitura = Leitura::factory()
            ->for($posto)
            ->for($turno)
            ->create(['data' => now()->toDateString()]);

        $response = $this->postJson(route('fechamentos.atualizar'), [
            'data' => now()->toDateString(),
            'turno_id' => $turno->id,
            'leituras' => [[
                'id' => $leitura->id,
                'leitura_inicial' => '200.000',
                'leitura_final' => '100.000',
                'preco_litro' => '6.0000',
            ]],
        ]);

        $this->assertSame(422, $response->getStatusCode(), $response->getContent());
        $errors = $response->json('errors');
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('leituras.0.leitura_final', $errors);
    }
}
