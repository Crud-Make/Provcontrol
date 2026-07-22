<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bico;
use App\Models\Bomba;
use App\Models\Combustivel;
use App\Models\Posto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bico>
 */
class BicoFactory extends Factory
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
            'bomba_id' => static fn (array $attributes): Factory => Bomba::factory()->state([
                'posto_id' => $attributes['posto_id'],
            ]),
            'combustivel_id' => static fn (array $attributes): Factory => Combustivel::factory()->state([
                'posto_id' => $attributes['posto_id'],
            ]),
            'numero' => fake()->unique()->numberBetween(1, 999),
            'ativo' => true,
            'ultima_afericao_em' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
