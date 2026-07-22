<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FechamentoStatus;
use App\Models\Fechamento;
use App\Models\Posto;
use App\Models\Turno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fechamento>
 */
class FechamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'posto_id' => Posto::factory(),
            'turno_id' => static fn (array $attributes): Factory => Turno::factory()->state([
                'posto_id' => $attributes['posto_id'],
            ]),
            'data' => fake()->dateTimeBetween('-1 year', 'now'),
            'status' => FechamentoStatus::Aberto,
            'total_vendas_bombas' => '0.00',
            'total_recebido' => '0.00',
            'diferenca' => '0.00',
            'observacoes' => null,
            'fechado_por' => null,
            'fechado_em' => null,
        ];
    }
}
