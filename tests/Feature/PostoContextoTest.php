<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Fechamento;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostoContextoTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_define_posto_ativo_na_sessao(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::query()->firstOrFail());

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSessionHas('posto_id', Posto::query()->value('id'));
    }

    public function test_fechamento_de_outro_posto_retorna_404(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::query()->firstOrFail())
            ->withSession(['posto_id' => Posto::query()->value('id')]);

        $outroPosto = Posto::query()->create([
            'nome' => 'Posto Teste B',
            'ativo' => true,
        ]);

        $turnoOutroPosto = Turno::query()->create([
            'posto_id' => $outroPosto->id,
            'nome' => 'Dia',
            'hora_inicio' => '06:00',
            'hora_fim' => '18:00',
            'ordem' => 1,
            'ativo' => true,
        ]);

        $fechamentoOutroPosto = Fechamento::query()->create([
            'posto_id' => $outroPosto->id,
            'turno_id' => $turnoOutroPosto->id,
            'data' => '2025-01-02',
            'status' => 'aberto',
            'total_vendas_bombas' => '0.00',
            'total_recebido' => '0.00',
            'diferenca' => '0.00',
        ]);

        $this->get(route('fechamentos.show', $fechamentoOutroPosto))
            ->assertNotFound();
    }
}
